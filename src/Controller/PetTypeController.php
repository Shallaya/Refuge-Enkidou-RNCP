<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\PetTypeRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PetTypeController extends AbstractController
{
    #[Route('/animaux/{slug}', name: 'app_pet_type_show')]
    public function show(
        string $slug, // Récupérer le slug en tant que string
        Request $request,
        PetTypeRepository $petTypeRepository,
        CategoryRepository $categoryRepo,
        ProductRepository $productRepo
    ): Response {
        // Chercher le PetType manuellement via le repository
        $petType = $petTypeRepository->findOneBy(['slug' => $slug]);
        
        if (!$petType) {
            throw $this->createNotFoundException('Type d\'animal introuvable.');
        }

        // Récupérer le filtre de catégorie depuis l'URL
        $categorySlug = $request->query->get('category');
        $category = null;
        
        // Récupérer toutes les catégories de ce type d'animal
        $categories = $petType->getCategories();
        
        // Filtrer les produits selon la catégorie sélectionnée
        if ($categorySlug) {
            $category = $categoryRepo->findOneBy([
                'slug' => $categorySlug,
                'petType' => $petType
            ]);           
            
        } 

        // Récupérer les produits (avec ou sans filtre de catégorie)
        $products = $productRepo->findByPetTypeAndCategory($petType, $category);
        
        return $this->render('pet_type/show.html.twig', [
            'petType' => $petType,
            'categories' => $categories,
            'products' => $products,
            'selectedCategorySlug' => $categorySlug,
        ]);
    }
}