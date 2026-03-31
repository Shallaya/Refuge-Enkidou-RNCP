<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Model\CartItem;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartService $cartService
    ) {}

    /**
     * Crée une commande à partir du panier actuel
     * 
     * @param array<string, string> $deliveryData
     * @return Order
     * @throws \Exception
     */
    public function createOrderFromCart(User $user, array $deliveryData): Order
    {
        $cart = $this->cartService->getCart();
        
        if (empty($cart)) {
            throw new \Exception('Le panier est vide');
        }

        $order = new Order();
        $order->setUser($user);
        
        // Informations de livraison
        $order->setDeliveryName($deliveryData['name']);
        $order->setDeliveryAddress($deliveryData['address']);
        $order->setDeliveryPostalCode($deliveryData['postal_code']);
        $order->setDeliveryCity($deliveryData['city']);
        $order->setDeliveryCountry($deliveryData['country']);
        $order->setDeliveryPhone($deliveryData['phone'] ?? null);

        $total = 0.0;
        $productRepository = $this->entityManager->getRepository(Product::class);

        // Création des items de commande
        foreach ($cart as $cartItem) {
                        
            // Récupération du produit complet depuis la base de données
            $product = $productRepository->find($cartItem->getProductId());
            
            if (!$product) {
                throw new \Exception(sprintf(
                    'Produit avec ID %d introuvable',
                    $cartItem->getProductId()
                ));
            }

            // Création de l'OrderItem
            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setProductName($cartItem->getProductName());
            
            // Conversion du prix en centimes vers euros (si ton price est en centimes)
            // Si ton price est déjà en euros, enlève la division par 100
            $priceInEuros = $cartItem->getPrice();
            $orderItem->setProductPrice((string) $priceInEuros);
            
            $orderItem->setQuantity($cartItem->getQuantity());
            
            // Calcul du total pour cet item
            $itemTotal = $priceInEuros * $cartItem->getQuantity();
            $orderItem->setTotal((string) $itemTotal);
            
            $order->addOrderItem($orderItem);
            $total += $itemTotal;
        }

        $order->setTotalAmount((string) $total);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateOrderStatus(Order $order, string $status): void
    {
        $order->setStatus($status);
        $this->entityManager->flush();
    }

    /**
     * Récupère les commandes d'un utilisateur
     * 
     * @return array<int, Order>
     */
    public function getUserOrders(User $user): array
    {
        return $this->entityManager
            ->getRepository(Order::class)
            ->findBy(['user' => $user], ['createdAt' => 'DESC']);
    }
}