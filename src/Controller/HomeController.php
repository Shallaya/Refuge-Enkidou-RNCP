<?php

namespace App\Controller;

use App\Services\YearlySalesGenerator;
use App\Repository\PromotionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private YearlySalesGenerator $yearlySalesGenerator,
        private PromotionRepository $promotionRepository
        ) {}

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $currentYear = (int) date('Y');
        // Génère automatiquement les soldes de l'année si elles n'existent pas
        $this->yearlySalesGenerator->ensureYearlySalesExist($currentYear);
        // Récupère toutes les promotions de l'année pour l'affichage
        $promotions = $this->promotionRepository->findByYear($currentYear);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'promotions' => $promotions,
        ]);
    }
}
