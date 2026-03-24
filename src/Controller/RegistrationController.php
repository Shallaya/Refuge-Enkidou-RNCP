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
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@example.com', 'Le Refuge d\'Enkidou'))
                    ->to((string) $user->getEmail())
                    ->subject('Confirme ton email pour Le Refuge d\'Enkidou')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // faites tout ce dont vous avez besoin ici, comme envoyer un e-mail, puis redirigez l'utilisateur vers une autre page

            // ❌ Ne pas connecter l’utilisateur automatiquement
            $this->addFlash('success', 'Inscription réussie ! Un email de confirmation vous a été envoyé.');
            return $this->redirectToRoute('app_login');
            }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $em): Response
    {
        $id = $request->get('id');

        if (!$id) {
            $this->addFlash('error', 'Lien invalide');
            return $this->redirectToRoute('home');
        }

        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable');
            return $this->redirectToRoute('home');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', 'Lien expiré ou invalide');
            return $this->redirectToRoute('home');
        }

        $this->addFlash('success', 'Votre adresse e-mail a bien été vérifiée. Vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('home');
    }

    #[Route('/resend-verification', name: 'app_resend_verification')]
    public function resendVerification(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        if ($user->isVerified()) {
            $this->addFlash('success', 'Email déjà vérifié');
            return $this->redirectToRoute('home');
        }

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('noreply@example.com', 'Le Refuge d\'Enkidou'))
                ->to((string) $user->getEmail())
                ->subject('Confirme ton email pour Le Refuge d\'Enkidou')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        $this->addFlash('success', 'Email renvoyé !');

        return $this->redirectToRoute('home');
    }
}
