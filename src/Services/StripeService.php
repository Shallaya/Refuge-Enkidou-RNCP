<?php

namespace App\Services;

use App\Entity\Order;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{
    public function __construct(
        private string $stripeSecretKey,
        private UrlGeneratorInterface $urlGenerator
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    /**
     * Crée une session de paiement Stripe Checkout
     */
    public function createCheckoutSession(Order $order): Session
    {
        $lineItems = [];

        foreach ($order->getOrderItems() as $item) {
            $productPrice = $item->getProductPrice();
            $productName = $item->getProductName();
            $quantity = $item->getQuantity();

            // Vérifications pour PHPStan
            if ($productPrice === null || $productName === null || $quantity === null) {
                throw new \RuntimeException('Données de produit invalides');
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int)((float)$productPrice), // Stripe utilise les centimes
                    'product_data' => [
                        'name' => $productName,
                    ],
                ],
                'quantity' => $quantity,
            ];
        }

        $user = $order->getUser();
        if ($user === null) {
            throw new \RuntimeException('La commande doit avoir un utilisateur');
        }

        $customerEmail = $user->getEmail();
        if ($customerEmail === null) {
            throw new \RuntimeException('L\'utilisateur doit avoir un email');
        }

        $orderReference = $order->getReference();
        if ($orderReference === null) {
            throw new \RuntimeException('La commande doit avoir une référence');
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate('app_order_success', [
                'reference' => $orderReference
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('app_order_cancel', [
                'reference' => $orderReference
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'customer_email' => $customerEmail,
            'metadata' => [
                'order_reference' => $orderReference,
            ],
        ]);

        return $session;
    }

    /**
     * Vérifie la signature du webhook Stripe
     */
    public function verifyWebhookSignature(string $payload, string $signature, string $secret): \Stripe\Event
    {
        return \Stripe\Webhook::constructEvent($payload, $signature, $secret);
    }
}