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
                'petType' => $chien,
                'image' => 'shampoing-solide-bio-chien.webp',
            ],
            [
                'name' => 'Shampoing Solide Bio Chat',
                'code' => 'SHAMP-SOL',
                'category' => $soinsChat,
                'petType' => $chat,
                'image' => 'shampoing-solide-bio-chat.webp',
            ],
            [
                'name' => 'Baume Coussinets Naturel',
                'code' => 'BAUME-COUS',
                'category' => $soinsChien,
                'petType' => $chien, $chat,
                'image' => 'baume-coussinets-naturel.webp',
            ],
            [
                'name' => 'Dentifrice Gel Naturel',
                'code' => 'DENT-GEL',
                'category' => $soinsChien,
                'petType' => $chien, $chat,
                'image' => 'dentifrice-gel-naturel.webp',
            ],
            [
                'name' => 'Brosse Bois Écologique',
                'code' => 'BROS-BOIS',
                'category' => $toilettageChien,
                'petType' => $chien,
                'image' => 'brosse-bois-ecologique.webp',
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
                ->addPetType($data['petType'])
                ->setImage($data['image'] ?? null);

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