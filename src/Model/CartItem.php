<?php

namespace App\Model;

class CartItem
{
    private int $variantId;
    private string $productName;
    private string $variantName;
    private int $price;
    private int $quantity;

    public function __construct(int $variantId, string $productName, string $variantName, int $price)
    {
        $this->variantId = $variantId;
        $this->productName = $productName;
        $this->variantName = $variantName;
        $this->price = $price;
        $this->quantity = 1;
    }

    public function getVariantId(): int
    {
        return $this->variantId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getVariantName(): string
    {
        return $this->variantName;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getTotal(): int
    {
        return $this->price * $this->quantity;
    }
}
