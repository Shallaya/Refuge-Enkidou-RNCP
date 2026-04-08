<?php

namespace App\Controller;

use App\Entity\ProductVariant;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    /**
     * Affiche la liste de tous les produits
     */
    #[Route('/produits', name: 'app_product_list')]
    public function list(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }
    
    /**
     * Affiche le détail d'un produit
     */
    #[Route('/{petTypeSlug}/produit/{slug}', name: 'app_product_show')]
    public function show(string $petTypeSlug, string $slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);

        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        // Vérifier que le produit est actif
        if (!$product->isActive()) {
            throw $this->createNotFoundException('Ce produit n\'est plus disponible.');
        }

        // Vérifier que le produit appartient bien au pet-type de l’URL
        $currentPetType = null;

        foreach ($product->getPetTypes() as $petType) {
            if ($petType->getSlug() === $petTypeSlug) {
                $currentPetType = $petType;
                break;
            }
        }

        if (!$currentPetType) {
            throw $this->createNotFoundException('Ce produit n\'appartient pas à ce type d’animal.');
        }
        
        // Récupérer uniquement les variantes actives
        $activeVariants = $product->getProductVariants()->filter(
            fn(ProductVariant $variant): bool => $variant->isActive() === true
        );
        
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'variants' => $activeVariants,
            'currentPetType' => $currentPetType,
        ]);
    }

    /**
     * Affiche les résultats de recherche de produits
     */
    #[Route('/recherche', name: 'app_product_search')]
    public function search(Request $request, ProductRepository $productRepository): Response
    {
        $query = trim((string) $request->query->get('q', ''));

        $products = [];

        if ($query !== '') {
            $products = $productRepository
                ->createQueryBuilder('p')
                ->leftJoin('p.petTypes', 'pt')->addSelect('pt')
                ->where('p.isActive = :active')
                ->andWhere('p.name LIKE :q OR p.shortDescription LIKE :q')
                ->setParameter('active', true)
                ->setParameter('q', '%' . $query . '%')
                ->orderBy('p.name', 'ASC')
                ->getQuery()
                ->getResult();
        }

        return $this->render('product/search.html.twig', [
            'products' => $products,
            'query' => $query,
        ]);
    }
}