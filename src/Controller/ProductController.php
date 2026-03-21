<?php

namespace App\Controller;

use App\Entity\ProductVariant;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    /**
     * Affiche la liste de tous les produits
     */
    #[Route('/produits', name: 'app_product_list')]
    public function list(): Response
    {
        // Pour plus tard : afficher tous les produits avec filtres
        return $this->render('product/list.html.twig');
    }
    
    /**
     * Affiche le détail d'un produit
     */
    #[Route('/produit/{slug}', name: 'app_product_show')]
    public function show(string $slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);

        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        // Vérifier que le produit est actif
        if (!$product->isActive()) {
            throw $this->createNotFoundException('Ce produit n\'est plus disponible.');
        }
        
        // Récupérer uniquement les variantes actives
        $activeVariants = $product->getProductVariants()->filter(
            fn(ProductVariant $variant): bool => $variant->isActive() === true
        );
        
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'variants' => $activeVariants,
        ]);
    }
}