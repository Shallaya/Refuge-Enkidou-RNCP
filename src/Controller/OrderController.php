<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Form\OrderType;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/order')]
#[IsGranted('ROLE_USER')]
#[IsGranted('IS_VERIFIED')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderService $orderService,
        private StripeService $stripeService,
        private CartService $cartService,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Formulaire de création de commande
     */
    #[Route('/checkout', name: 'app_order_checkout')]
    public function checkout(Request $request): Response
    {
    //     dump([
    //     'method' => $request->getMethod(),
    //     'isSubmitted' => $request->isMethod('POST'),
    //     'data' => $request->request->all(),
    // ]);
        $cart = $this->cartService->getCart();
        
        if (empty($cart)) {
            $this->addFlash('warning', 'Votre panier est vide');
            return $this->redirectToRoute('app_cart_index');
        }

        $form = $this->createForm(OrderType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string, string> $deliveryData */
            $deliveryData = $form->getData();
            
            // Récupération et vérification de l'utilisateur
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour commander');
            }
            
            try {
                // Création de la commande
                $order = $this->orderService->createOrderFromCart($user, $deliveryData);

                // Création de la session Stripe
                $session = $this->stripeService->createCheckoutSession($order);
                
                // Sauvegarde de l'ID de session
                $order->setStripeSessionId($session->id);
                $this->entityManager->flush();

                // Vérification que l'URL existe
                $checkoutUrl = $session->url;
                if ($checkoutUrl === null) {
                    throw new \RuntimeException('Stripe n\'a pas retourné d\'URL de paiement');
                }

                // Redirection vers Stripe Checkout
                return $this->redirect($checkoutUrl);
                
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création de la commande : ' . $e->getMessage());
            }
        }

        return $this->render('order/checkout.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart,
            'total' => $this->cartService->getTotal(),
        ]);
    }

    /**
     * Page de succès après paiement
     */
    #[Route('/success/{reference}', name: 'app_order_success')]
    public function success(string $reference): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $order = $this->entityManager
            ->getRepository(Order::class)
            ->findOneBy(['reference' => $reference]);

        if (!$order || $order->getUser() !== $user) {
            throw $this->createNotFoundException('Commande introuvable');
        }

        // Vider le panier après succès
        $this->cartService->clear();

        return $this->render('order/success.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * Page d'annulation
     */
    #[Route('/cancel/{reference}', name: 'app_order_cancel')]
    public function cancel(string $reference): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $order = $this->entityManager
            ->getRepository(Order::class)
            ->findOneBy(['reference' => $reference]);

        if (!$order || $order->getUser() !== $user) {
            throw $this->createNotFoundException('Commande introuvable');
        }

        return $this->render('order/cancel.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * Liste des commandes de l'utilisateur
     */
    #[Route('/my-orders', name: 'app_order_list')]
    public function list(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $orders = $this->orderService->getUserOrders($user);

        return $this->render('order/list.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * Détail d'une commande
     */
    #[Route('/{reference}', name: 'app_order_show')]
    public function show(string $reference): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $order = $this->entityManager
            ->getRepository(Order::class)
            ->findOneBy(['reference' => $reference]);

        if (!$order || $order->getUser() !== $user) {
            throw $this->createNotFoundException('Commande introuvable');
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}