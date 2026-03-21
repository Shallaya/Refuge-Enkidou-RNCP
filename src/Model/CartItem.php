<?php

namespace App\Model;

class CartItem
{
    private int $productId;
    private string $productName;
    private int $price;
    private int $quantity;

    public function __construct(int $productId, string $productName, int $price)
    {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->price = $price;
        $this->quantity = 1;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
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
