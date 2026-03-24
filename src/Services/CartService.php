<?php

namespace App\Services;

use App\Model\CartItem;
use App\Entity\ProductVariant;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    // Clé de session pour stocker le panier
    private const CART_KEY = 'shopping_cart';

    public function __construct(
        private RequestStack $requestStack,
    ) {}


    /**
     * Ajoute une variante au panier
     */
    public function addItem(ProductVariant $variant): void
    {
        
        // Vérifier le stock
        if ($variant->getStock() <= 0) {
            return;
        }

        $variantId = $variant->getId();

        if ($variantId === null) {
            throw new \LogicException('Variant sans ID');
        }

        $cart = $this->getCart();

        if (isset($cart[$variantId])) {
            $cart[$variantId]->setQuantity(
                $cart[$variantId]->getQuantity() + 1
            );
        } else {
            $cart[$variantId] = new CartItem(
                $variantId,
                $variant->getProduct()->getName(),
                (int) ((float) $variant->getPrice() * 100) // conversion propre
            );
        }

        $this->save($cart);
    }
    

    /**
     * @return \App\Model\CartItem[]|array<int, \App\Model\CartItem>
     */
    public function getCart(): array
    {
        $cart = $this->requestStack
            ->getSession()
            // Retourne un tableau de CartItem ou un tableau vide si le panier n'existe pas encore
            ->get(self::CART_KEY, []);

        if (!is_array($cart)) {
            return [];
        }

        $filtered = [];
        foreach ($cart as $k => $v) {
            if ($v instanceof CartItem) {
                $filtered[(int) $k] = $v;
            }
        }

        return $filtered;
    }

    public function removeItem(int $productId): void
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        $this->save($cart);
    }

    public function clear(): void
    {
        $this->save([]);
    }

    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }

    public function getCount(): int
    {
        $count = 0;
        foreach ($this->getCart() as $item) {
            $count += $item->getQuantity();
        }
        return $count;
    }

    /**
     * @param \App\Model\CartItem[]|array<int, \App\Model\CartItem> $cart
     */
    private function save(array $cart): void
    {
        $normalized = [];
        foreach ($cart as $k => $v) {
            $normalized[(int) $k] = $v;
        }

        $this->requestStack
            ->getSession()
            ->set(self::CART_KEY, $normalized);
    }
};
