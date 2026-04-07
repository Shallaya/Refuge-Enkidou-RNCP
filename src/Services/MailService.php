<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\User;
use App\Model\ContactMessage;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class MailService
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private EmailVerifier $emailVerifier,
        private string $senderEmail,
        private string $senderName
    ) {}

    /**
     * Enregistre un nouvel utilisateur
     */
    public function registerUser(User $user, string $plainPassword): void
    {
        // Hash password
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $plainPassword)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Envoie un email de confirmation d'inscription avec un lien de vérification
     */
    public function sendConfirmationEmail(User $user, string $verifyRouteName, string $resendUrl): void
    {
        $this->emailVerifier->sendEmailConfirmation(
            $verifyRouteName,
            $user,
            (new TemplatedEmail())
                ->from(new Address($this->senderEmail, $this->senderName))
                ->to((string) $user->getEmail())
                ->subject('Confirme ton email')
                ->htmlTemplate('mails/confirmation_email.html.twig')
                ->context([
                    'resendUrl' => $resendUrl,
                ])
        );
    }

    /**
     * Gère la confirmation de l'email à partir du lien de vérification      * 
     * @throws VerifyEmailExceptionInterface Si la validation échoue (lien invalide, expiré, etc.)
     */
    public function verifyEmail(Request $request, User $user): void
    {
        $this->emailVerifier->handleEmailConfirmation($request, $user);
        $this->entityManager->refresh($user);
    }

    /**
     * Renvoie un email de confirmation d'inscription
     */
    public function resendVerificationEmail(User $user, string $verifyRouteName, string $resendUrl): void
    {
        $this->sendConfirmationEmail($user, $verifyRouteName, $resendUrl);
    }

    /**
     * Envoie un email de confirmation de commande
     * 
     * @throws \RuntimeException Si l'utilisateur n'a pas d'email
     */
    public function sendOrderConfirmation(Order $order): void
    {
        $user = $order->getUser();
        
        if ($user === null) {
            throw new \RuntimeException('La commande n\'a pas d\'utilisateur associé');
        }

        $userEmail = $user->getEmail();
        
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->senderName))
            ->to((string) $userEmail)
            ->subject('Confirmation de commande - ' . $order->getReference())
            ->htmlTemplate('mails/order_confirmation.html.twig')
            ->context([
                'order' => $order,
            ]);

        $this->mailer->send($email);
    }

    /**
     * Envoie un email de notification d'expédition
     * 
     * @throws \RuntimeException Si l'utilisateur n'a pas d'email
     */
    public function sendShippingNotification(Order $order, string $trackingNumber): void
    {
        $user = $order->getUser();
        
        if ($user === null) {
            throw new \RuntimeException('La commande n\'a pas d\'utilisateur associé');
        }

        $userEmail = $user->getEmail();
        
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->senderName))
            ->to((string) $userEmail)
            ->subject('Votre commande ' . $order->getReference() . ' a été expédiée')
            ->htmlTemplate('mails/shipping_notification.html.twig')
            ->context([
                'order' => $order,
                'trackingNumber' => $trackingNumber,
            ]);

        $this->mailer->send($email);
    }

    public function sendContact(ContactMessage $contactMessage): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($contactMessage->getEmail(), $contactMessage->getName()))
            ->to($this->senderEmail)
            ->subject('[Contact] ' . $contactMessage->getSubject())
            ->htmlTemplate('mails/contact.html.twig')
            ->context([
                'name' => $contactMessage->getName(),
                'userEmail' => $contactMessage->getEmail(),
                'subject' => $contactMessage->getSubject(),
                'message' => $contactMessage->getMessage(),
            ]);

        $this->mailer->send($email);
    }
}