<?php

namespace App\Controller;

use App\Services\CartService;
use App\Repository\ProductVariantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_VERIFIED')]
final class CartController extends AbstractController
{
    public function __construct(
        private CartService $cartService
    ){}

    #[Route('/cart', name: 'app_cart_index')]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $this->cartService->getCart(),
            'total' => $this->cartService->getTotal(),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(int $id, Request $request, ProductVariantRepository $variantRepo): Response
    {
        $variantId = $request->request->get('variant_id');
        if (!$variantId) {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['error' => 'Aucune variante sélectionnée.'], 400);
            }
            $this->addFlash('danger', 'Aucune variante sélectionnée.');
            return $this->redirectToRoute('app_cart_index');
        }
        $variant = $variantRepo->find($variantId);
        if (!$variant) {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['error' => 'Variante introuvable.'], 404);
            }
            $this->addFlash('danger', 'Variante introuvable.');
            return $this->redirectToRoute('app_cart_index');
        }
        $this->cartService->addItem($variant);
        $this->updateCartSession($request);
        $this->addFlash('success', 'Produit ajouté au panier !');
        return $this->redirectAfterCart($request);
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(int $id, Request $request): Response
    {
        $this->cartService->removeItem($id);
        $this->updateCartSession($request);
        $this->addFlash('info', 'Produit retiré du panier.');
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(Request $request): Response
    {
                $this->cartService->clear();
        $this->updateCartSession($request);
        $this->addFlash('warning', 'Panier vidé.');
        return $this->redirectToRoute('app_cart_index');
    }

    private function updateCartSession(Request $request): void
    {
        $cartCount = $this->cartService->getCount();
        $request->getSession()->set('cart_count', $cartCount);
    }

    private function redirectAfterCart(Request $request): Response
    {
        $referer = $request->headers->get('referer');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('home');
    }
}
