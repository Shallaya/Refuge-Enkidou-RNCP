<?php

namespace App\Services;

use App\Entity\Order;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class OrderEmailService
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

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
            ->from(new Address('noreply@tonsite.com', 'Votre Site E-commerce'))
            ->to((string) $userEmail)
            ->subject('Confirmation de commande - ' . $order->getReference())
            ->htmlTemplate('emails/order_confirmation.html.twig')
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
            ->from(new Address('noreply@tonsite.com', 'Votre Site E-commerce'))
            ->to((string) $userEmail)
            ->subject('Votre commande ' . $order->getReference() . ' a été expédiée')
            ->htmlTemplate('emails/shipping_notification.html.twig')
            ->context([
                'order' => $order,
                'trackingNumber' => $trackingNumber,
            ]);

        $this->mailer->send($email);
    }
}