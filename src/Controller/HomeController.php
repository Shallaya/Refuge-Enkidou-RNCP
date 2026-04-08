<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PetTypeRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductVariantRepository;
use App\Services\YearlySalesGenerator;
use App\Repository\PromotionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private YearlySalesGenerator $yearlySalesGenerator,
        private PromotionRepository $promotionRepository
        ) {}

    #[Route('/', name: 'home')]
    public function index(PetTypeRepository $petTypeRepository, CategoryRepository $categoryRepo, ProductRepository $productRepo, ProductVariantRepository $productVariantRepo): Response
    {
        $currentYear = (int) date('Y');
        // Génère automatiquement les soldes de l'année si elles n'existent pas
        $this->yearlySalesGenerator->ensureYearlySalesExist($currentYear);
        // Récupère toutes les promotions de l'année pour l'affichage
        $promotions = $this->promotionRepository->findByYear($currentYear);

        $petTypes = $petTypeRepository->findAll();

        $featuredCategories = $categoryRepo->findBy(['parent' => null]);

        $featuredVariants = $productVariantRepo->findFeaturedVariants(8);

        return $this->render('home/index.html.twig', [
            'promotions' => $promotions,
            'petTypes' => $petTypes,
            'featuredCategories'=> $featuredCategories,
            'featuredVariants'  => $featuredVariants,
        ]);
    }

    #[Route('/a-propos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }
}
