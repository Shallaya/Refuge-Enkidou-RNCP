<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\PetType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $chien = $this->getReference('pettype-chien', PetType::class);
        $chat  = $this->getReference('pettype-chat', PetType::class);

        $categoryRepo = $manager->getRepository(Category::class);

        $soinsChien = $categoryRepo->findOneBy([
            'name' => 'Produits de soin',
            'petType' => $chien
        ]);

        $toilettageChien = $categoryRepo->findOneBy([
            'name' => 'Toilettage',
            'petType' => $chien
        ]);

        $soinsChat = $categoryRepo->findOneBy([
            'name' => 'Produits de soin',
            'petType' => $chat
        ]);

        $toilettageChat = $categoryRepo->findOneBy([
            'name' => 'Toilettage',
            'petType' => $chat
        ]);

        $products = [

            [
                'name' => 'Shampoing Solide Bio Chien',
                'code' => 'SHAMP-SOL',
                'category' => $soinsChien,
                'petType' => $chien
            ],
            [
                'name' => 'Shampoing Solide Bio Chat',
                'code' => 'SHAMP-SOL',
                'category' => $soinsChat,
                'petType' => $chat
            ],
            [
                'name' => 'Baume Coussinets Naturel',
                'code' => 'BAUME-COUS',
                'category' => $soinsChien,
                'petType' => $chien
            ],
            [
                'name' => 'Baume Coussinets Naturel',
                'code' => 'BAUME-COUS',
                'category' => $soinsChat,
                'petType' => $chat
            ],
            [
                'name' => 'Dentifrice Solide Naturel',
                'code' => 'DENT-SOL',
                'category' => $soinsChien,
                'petType' => $chien
            ],
            [
                'name' => 'Brosse Bois Écologique',
                'code' => 'BROS-BOIS',
                'category' => $toilettageChien,
                'petType' => $chien
            ],
        ];

        foreach ($products as $data) {

            $product = new Product();
            $product
                ->setName($data['name'])
                ->setShortDescription('Produit écoresponsable pour animaux')
                ->setDescription('Produit zéro déchet conçu pour le bien-être animal.')
                ->setIsActive(true)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setCode($data['code'])
                ->setCategory($data['category'])
                ->setPetType($data['petType']);

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}