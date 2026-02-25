<?php

namespace App\Services;

use App\Entity\Promotion;
use App\Enum\PromotionCategory;
use App\Enum\DiscountType;
use App\Repository\PromotionRepository;
use Doctrine\ORM\EntityManagerInterface;

class YearlySalesGenerator
{
    public function __construct(
        private PromotionRepository $promotionRepository,
        private EntityManagerInterface $em,
        private SalesPeriodCalculator $calculator
    ) {}

    public function ensureYearlySalesExist(int $year): void
    {
        $this->createIfNotExists(PromotionCategory::WINTER_SALES, $year);
        $this->createIfNotExists(PromotionCategory::SUMMER_SALES, $year);
    }

    private function createIfNotExists(PromotionCategory $category, int $year): void
    {
        $existing = $this->promotionRepository->findOneBy([
            'prCategory' => $category,
            'year' => $year
        ]);

        if ($existing) {
            return;
        }

        if ($category === PromotionCategory::WINTER_SALES) {
            [$start, $end] = $this->calculator->getWinterSales($year);
            $name = "Soldes Hiver $year";
        } else {
            [$start, $end] = $this->calculator->getSummerSales($year);
            $name = "Soldes Été $year";
        }

        $promotion = new Promotion();
        $promotion->setName($name);
        $promotion->setPrCategory($category);
        $promotion->setYear($year);
        $promotion->setStartDate($start);
        $promotion->setEndDate($end);
        $promotion->setDiscountValue("20.00");
        $promotion->setDiscountType(DiscountType::PERCENTAGE);
        $promotion->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($promotion);
        $this->em->flush();
    }
}