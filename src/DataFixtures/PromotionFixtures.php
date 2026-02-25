<?php

namespace App\DataFixtures;

use App\Services\YearlySalesGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PromotionFixtures extends Fixture
{
    public function __construct(
        private YearlySalesGenerator $yearlySalesGenerator
    ) {}

    public function load(ObjectManager $manager): void
    {
        $currentYear = (int) date('Y');
        $this->yearlySalesGenerator->ensureYearlySalesExist($currentYear);
    }
}
