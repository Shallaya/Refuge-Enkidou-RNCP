<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Entity\ProductVariant;
use App\Model\CartItem;
use App\Services\CartService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartServiceTest extends TestCase
{
    private SessionInterface&MockObject $session;
    private CartService $cartService;

    protected function setUp(): void
    {
        $this->session = $this->createMock(SessionInterface::class);

        $request = new Request();
        $request->setSession($this->session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $this->cartService = new CartService($requestStack);
    }


    private function createVariant(
        int $id,
        string $productName = 'Produit test',
        string $variantName = 'Taille M',
        ?string $price = '19.90',
        int $stock = 10
    ): ProductVariant {
        $product = $this->createMock(Product::class);
        $product->method('getName')->willReturn($productName);

        $variant = $this->createMock(ProductVariant::class);
        $variant->method('getId')->willReturn($id);
        $variant->method('getProduct')->willReturn($product);
        $variant->method('getVariantName')->willReturn($variantName);
        $variant->method('getPrice')->willReturn($price);
        $variant->method('getStock')->willReturn($stock);

        return $variant;
    }

    public function testAddItemAddsNewItemToCart(): void
    {
        $variant = $this->createVariant(1, 'Shampoing', '250 ml', '12.50', 5);

        $this->session
            ->method('get')
            ->with('shopping_cart', [])
            ->willReturn([]);

        $this->session
            ->expects($this->once())
            ->method('set')
            ->with(
                'shopping_cart',
                $this->callback(function (array $cart) {
                    return isset($cart[1])
                        && $cart[1] instanceof CartItem
                        && $cart[1]->getVariantId() === 1
                        && $cart[1]->getProductName() === 'Shampoing'
                        && $cart[1]->getVariantName() === '250 ml'
                        && $cart[1]->getPrice() === 1250
                        && $cart[1]->getQuantity() === 1;
                })
            );

        $this->cartService->addItem($variant);
    }

    public function testAddItemIncrementsQuantityIfItemAlreadyExists(): void
    {
        $existingItem = new CartItem(1, 'Shampoing', '250 ml', 1250);

        $this->session
            ->method('get')
            ->with('shopping_cart', [])
            ->willReturn([1 => $existingItem]);

        $this->session
            ->expects($this->once())
            ->method('set')
            ->with(
                'shopping_cart',
                $this->callback(function (array $cart) {
                    return isset($cart[1])
                        && $cart[1]->getQuantity() === 2;
                })
            );

        $variant = $this->createVariant(1, 'Shampoing', '250 ml', '12.50', 5);

        $this->cartService->addItem($variant);
    }

    public function testAddItemDoesNothingIfStockIsZero(): void
    {
        $variant = $this->createVariant(1, 'Shampoing', '250 ml', '12.50', 0);

        $this->session
            ->expects($this->never())
            ->method('set');

        $this->cartService->addItem($variant);
    }

    public function testUpdateQuantityChangesQuantity(): void
    {
        $item = new CartItem(1, 'Shampoing', '250 ml', 1250);

        $this->session
            ->method('get')
            ->with('shopping_cart', [])
            ->willReturn([1 => $item]);

        $this->session
            ->expects($this->once())
            ->method('set')
            ->with(
                'shopping_cart',
                $this->callback(function (array $cart) {
                    return isset($cart[1]) && $cart[1]->getQuantity() === 3;
                })
            );

        $this->cartService->updateQuantity(1, 3);
    }

    public function testUpdateQuantityRemovesItemIfQuantityIsLessThanOne(): void
    {
        $item = new CartItem(1, 'Shampoing', '250 ml', 1250);

        $this->session
            ->method('get')
            ->with('shopping_cart', [])
            ->willReturn([1 => $item]);

        $this->session
            ->expects($this->once())
            ->method('set')
            ->with('shopping_cart', []);

        $this->cartService->updateQuantity(1, 0);
    }

    public function testRemoveItemRemovesItemFromCart(): void
    {
        $item = new CartItem(1, 'Shampoing', '250 ml', 1250);

        $this->session
            ->method('get')
            ->with('shopping_cart', [])
            ->willReturn([1 => $item]);

        $this->session
            ->expects($this->once())
            ->method('set')
            ->with('shopping_cart', []);

        $this->cartService->removeItem(1);
    }

    public function testClearEmptiesCart(): void
    {
        $this->session
            ->expects($this->once())
            ->method('set')
            ->with('shopping_cart', []);

        $this->cartService->clear();
    }

    public function testGetTotalReturnsCartTotal(): void
    {
        $item1 = new CartItem(1, 'Produit A', 'Variante A', 1000);
        $item1->setQuantity(2); // 2000

        $item2 = new CartItem(2, 'Produit B', 'Variante B', 500);
        $item2->setQuantity(3); // 1500

        $this->session
            ->method('get')
            ->with('shopping_cart', [])
            ->willReturn([
                1 => $item1,
                2 => $item2,
            ]);

        $this->assertSame(3500, $this->cartService->getTotal());
    }

    public function testGetCountReturnsTotalQuantity(): void
    {
        $item1 = new CartItem(1, 'Produit A', 'Variante A', 1000);
        $item1->setQuantity(2);

        $item2 = new CartItem(2, 'Produit B', 'Variante B', 500);
        $item2->setQuantity(3);

        $this->session
            ->method('get')
            ->with('shopping_cart', [])
            ->willReturn([
                1 => $item1,
                2 => $item2,
            ]);

        $this->assertSame(5, $this->cartService->getCount());
    }
}