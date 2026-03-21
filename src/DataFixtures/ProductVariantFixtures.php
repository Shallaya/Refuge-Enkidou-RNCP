<?php

namespace App\DataFixtures;

use App\Entity\ProductVariant;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductVariantFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $productRepo = $manager->getRepository(Product::class);

        $variants = [

            [
                'productName' => 'Shampoing Solide Bio Chien',
                'weightValue' => '80',
                'weightUnit' => 'g',
                'price' => '9.90',
                'stock' => 50,
            ],
            [
                'productName' => 'Shampoing Solide Bio Chien',
                'weightValue' => '120',
                'weightUnit' => 'g',
                'price' => '14.90',
                'stock' => 30,
            ],
            [
                'productName' => 'Shampoing Solide Bio Chat',
                'weightValue' => '80',
                'weightUnit' => 'g',
                'price' => '12.90',
                'stock' => 40,
            ],
            [
                'productName' => 'Shampoing Solide Bio Chat',
                'weightValue' => '120',
                'weightUnit' => 'g',
                'price' => '18.90',
                'stock' => 20,
            ],
            [
                'productName' => 'Baume Coussinets Naturel',
                'volumeValue' => '30',
                'volumeUnit' => 'ml',
                'price' => '12.90',
                'stock' => 25,
            ],
            [
                'productName' => 'Brosse Bois Écologique',
                'material' => 'Bois FSC',
                'price' => '18.90',
                'color' => 'jaune',
                'size' => 'S',
                'stock' => 15,
            ],
            [
                'productName' => 'Brosse Bois Écologique',
                'material' => 'Bois FSC',
                'price' => '18.90',
                'color' => 'vert',
                'size' => 'S',
                'stock' => 15,
            ],
            [
                'productName' => 'Brosse Bois Écologique',
                'material' => 'Bois FSC',
                'price' => '18.90',
                'color' => 'jaune',
                'size' => 'M',
                'stock' => 15,
            ],
            [
                'productName' => 'Brosse Bois Écologique',
                'material' => 'Bois FSC',
                'price' => '18.90',
                'color' => 'vert',
                'size' => 'M',
                'stock' => 15,
            ],
        ];

        foreach ($variants as $data) {

            $product = $productRepo->findOneBy([
                'name' => $data['productName']
            ]);

            $variant = new ProductVariant();
            $variant
                ->setProduct($product)
                ->setStock($data['stock'])
                ->setPrice($data['price'])
                ->setIsActive(true);

            if (isset($data['material'])) {
                $variant->setMaterial($data['material']);
            }

            if (isset($data['size'])) {
                $variant->setSize($data['size']);
            }

            if (isset($data['weightValue'])) {
                $variant->setWeightValue($data['weightValue']);
            }

            if (isset($data['weightUnit'])) {
                $variant->setWeightUnit($data['weightUnit']);
            }

            if (isset($data['volumeValue'])) {
                $variant->setVolumeValue($data['volumeValue']);
            }

            if (isset($data['volumeUnit'])) {
                $variant->setVolumeUnit($data['volumeUnit']);
            }

            if (isset($data['color'])) {
                $variant->setColor($data['color']);
            }     

            $manager->persist($variant);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
        ];
    }
}