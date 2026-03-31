<?php

namespace App\Twig;

use App\Services\CartService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CartExtension extends AbstractExtension
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cart_count', [$this, 'getCartCount']),
            new TwigFunction('cart_total', [$this, 'getCartTotal']),
        ];
    }

    /**
     * Retourne le nombre total d'articles dans le panier
     */
    public function getCartCount(): int
    {
        $cart = $this->cartService->getCart();
        $count = 0;
        
        foreach ($cart as $item) {
            $count += $item->getQuantity();
        }
        
        return $count;
    }

    /**
     * Retourne le montant total du panier
     */
    public function getCartTotal(): float
    {
        return $this->cartService->getTotal();
    }
}