<?php

namespace App\DataFixtures;

use App\Entity\ProductVariant;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductVariantFixtures extends Fixture implements DependentFixtureInterface
{
    private const FIELD_MAP = [
        'material' => 'setMaterial',
        'size' => 'setSize',
        'weightValue' => 'setWeightValue',
        'weightUnit' => 'setWeightUnit',
        'volumeValue' => 'setVolumeValue',
        'volumeUnit' => 'setVolumeUnit',
        'color' => 'setColor',
    ];

    public function load(ObjectManager $manager): void
    {
        $productRepo = $manager->getRepository(Product::class);
        $variantRepo = $manager->getRepository(ProductVariant::class);

        $variants = [

            [
                'productName' => 'Shampoing Solide Bio Chien',
                'weightValue' => '80',
                'weightUnit' => 'g',
                'price' => '9.90',
                'stock' => 50,
                'variantName' => '80g',
            ],
            [
                'productName' => 'Shampoing Solide Bio Chien',
                'weightValue' => '120',
                'weightUnit' => 'g',
                'price' => '14.90',
                'stock' => 30,
                'variantName' => '120g',
            ],
            [
                'productName' => 'Shampoing Solide Bio Chat',
                'weightValue' => '80',
                'weightUnit' => 'g',
                'price' => '12.90',
                'stock' => 40,
                'variantName' => '80g',
            ],
            [
                'productName' => 'Shampoing Solide Bio Chat',
                'weightValue' => '120',
                'weightUnit' => 'g',
                'price' => '18.90',
                'stock' => 20,
                'variantName' => '120g',
            ],
            [
                'productName' => 'Baume Coussinets Naturel',
                'volumeValue' => '30',
                'volumeUnit' => 'ml',
                'price' => '12.90',
                'stock' => 25,
                'variantName' => '30ml',
            ],
            [
                'productName' => 'Brosse Bois Écologique',
                'material' => 'Bois FSC',
                'price' => '18.90',
                'color' => 'jaune',
                'size' => 'S',
                'stock' => 15,
                'variantName' => 'S Jaune',
            ],
            [
                'productName' => 'Brosse Bois Écologique',
                'material' => 'Bois FSC',
                'price' => '18.90',
                'color' => 'vert',
                'size' => 'S',
                'stock' => 15,
                'variantName' => 'S Vert',
            ],
            [
                'productName' => 'Brosse Bois Écologique',
                'material' => 'Bois FSC',
                'price' => '22.90',
                'color' => 'jaune',
                'size' => 'M',
                'stock' => 15,
                'variantName' => 'M Jaune',
            ],
            [
                'productName' => 'Brosse Bois Écologique',
                'material' => 'Bois FSC',
                'price' => '22.90',
                'color' => 'vert',
                'size' => 'M',
                'stock' => 15,
                'variantName' => 'M Vert',
            ],
        ];

        foreach ($variants as $data) {

            $product = $productRepo->findOneBy([
                'name' => $data['productName']
            ]);

            // construire des critères dynamiquement pour vérifier l'existence de la variante
            $criteria = ['product' => $product];
            
            foreach (self::FIELD_MAP as $field => $setter) {
                if (isset($data[$field])) {
                    $criteria[$field] = $data[$field];
                }
            }

            if ($variantRepo->findOneBy($criteria)) {
                continue; // Skip cette variante, elle existe déjà
            }

            if ($product === null) {
                throw new \LogicException('Le produit doit être défini avant de créer un variant');
            }

            $variant = (new ProductVariant())
                ->setProduct($product)
                ->setStock($data['stock'])
                ->setPrice($data['price'])
                ->setIsActive(true)
                ->setVariantName($data['variantName'] ?? null);

            // setters dynamiques
            foreach (self::FIELD_MAP as $field => $setter) {
                if (isset($data[$field])) {
                    $variant->$setter($data[$field]);
                }
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