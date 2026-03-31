<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encoder le mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $plainPassword)
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // Génération du lien de renvoi
            $resendUrl = $this->generateUrl('app_resend_verification', [
                'id' => $user->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            // Envoi de l'email avec le contexte
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@refugeenkidou.com', 'Le Refuge d\'Enkidou'))
                    ->to((string) $user->getEmail())
                    ->subject('Confirme ton email pour Le Refuge d\'Enkidou')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'resendUrl' => $resendUrl,
                    ])
            );

            $this->addFlash('success', 'Inscription réussie ! Un email de confirmation vous a été envoyé.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->query->get('id');

        if (!$id) {
            $this->addFlash('error', 'Lien invalide : identifiant manquant');
            return $this->redirectToRoute('home');
        }

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable');
            return $this->redirectToRoute('home');
        }

        // Vérification que l'utilisateur n'est pas déjà vérifié
        if ($user->isVerified()) {
            $this->addFlash('info', 'Votre email est déjà vérifié.');
            return $this->redirectToRoute('app_login');
        }

        try {
                       
            // Cette méthode fait déjà le persist et le flush dans EmailVerifier
            $this->emailVerifier->handleEmailConfirmation($request, $user);
            
            // Rafraîchir l'entité depuis la base de données pour vérifier
            $entityManager->refresh($user);
            
            $this->addFlash('success', 'Votre adresse e-mail a bien été vérifiée. Vous pouvez maintenant vous connecter.');
            
            return $this->redirectToRoute('app_login');
            
        } catch (VerifyEmailExceptionInterface $e) {
            
            $this->addFlash('error', 'Lien expiré ou invalide : ' . $e->getReason());
            
            // Génération du lien de renvoi en cas d'erreur
            $resendUrl = $this->generateUrl('app_resend_verification', [
                'id' => $user->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            
            $this->addFlash('info', 'Vous pouvez demander un nouvel email : <a href="' . $resendUrl . '">Renvoyer l\'email</a>');
            
            return $this->redirectToRoute('home');
        }
    }

    #[Route('/resend-verification/{id}', name: 'app_resend_verification')]
    public function resendVerification(User $user): Response
    {
        if ($user->isVerified()) {
            $this->addFlash('success', 'Email déjà vérifié');
            return $this->redirectToRoute('home');
        }

        // Génération du lien de renvoi
        $resendUrl = $this->generateUrl('app_resend_verification', [
            'id' => $user->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        // Envoi de l'email
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('noreply@refugeenkidou.com', 'Le Refuge d\'Enkidou'))
                ->to((string) $user->getEmail())
                ->subject('Confirme ton email pour Le Refuge d\'Enkidou')
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    'resendUrl' => $resendUrl,
                ])
        );

        $this->addFlash('success', 'Email de confirmation renvoyé !');

        return $this->redirectToRoute('app_login');
    }
}