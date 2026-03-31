<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\OrderEmailService;
use App\Services\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebhookController extends AbstractController
{
    public function __construct(
        private StripeService $stripeService,
        private EntityManagerInterface $entityManager,
        private OrderEmailService $orderEmailService,
        private string $stripeWebhookSecret
    ) {}

    #[Route('/webhook/stripe', name: 'app_webhook_stripe', methods: ['POST'])]
    public function stripe(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->headers->get('stripe-signature');

        if (!$signature) {
            return new Response('Missing signature', 400);
        }

        try {
            $event = $this->stripeService->verifyWebhookSignature(
                $payload,
                $signature,
                $this->stripeWebhookSecret
            );

            // Traitement selon le type d'événement
            switch ($event->type) {
                case 'checkout.session.completed':
                    /** @var Session $session */
                    $session = $event->data->object;
                    $this->handleCheckoutSessionCompleted($session);
                    break;

                case 'payment_intent.succeeded':
                    /** @var PaymentIntent $paymentIntent */
                    $paymentIntent = $event->data->object;
                    $this->handlePaymentIntentSucceeded($paymentIntent);
                    break;

                case 'payment_intent.payment_failed':
                    /** @var PaymentIntent $paymentIntent */
                    $paymentIntent = $event->data->object;
                    $this->handlePaymentIntentFailed($paymentIntent);
                    break;
            }

            return new Response('Webhook handled', 200);
            
        } catch (\Exception $e) {
            return new Response('Webhook error: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Gestion de la session Stripe complétée
     */
    private function handleCheckoutSessionCompleted(Session $session): void
    {
        // Récupération de la référence de commande depuis les metadata
        $orderReference = $session->metadata->order_reference ?? null;
        
        $order = $this->entityManager
            ->getRepository(Order::class)
            ->findOneBy(['reference' => $orderReference]);

        $email = $order?->getUser()?->getEmail() ?? 'no email';
    
        if ($order) {
            $order->setStatus('paid');
            
            // Le payment_intent peut être une string ou un objet PaymentIntent
            $paymentIntentId = is_string($session->payment_intent) 
                ? $session->payment_intent 
                : $session->payment_intent->id ?? null;
                
            if ($paymentIntentId) {
                $order->setStripePaymentIntentId($paymentIntentId);
            }
            
            $this->entityManager->flush();

            // Envoi de l'email de confirmation
            try {
                $this->orderEmailService->sendOrderConfirmation($order);
            } catch (\Exception $e) {
                // Logger l'erreur mais ne pas bloquer le webhook
                // En production, tu peux utiliser un logger
            }
        }
    }

    /**
     * Gestion du paiement réussi
     */
    private function handlePaymentIntentSucceeded(PaymentIntent $paymentIntent): void
    {
        // Logique supplémentaire si nécessaire
    }

    /**
     * Gestion de l'échec de paiement
     */
    private function handlePaymentIntentFailed(PaymentIntent $paymentIntent): void
    {
        // Gestion des échecs de paiement
    }
}