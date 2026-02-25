<?php

namespace App\DataFixtures;

use App\Entity\EcoLabel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EcoLabelFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $ecoLabels = [
            [
                'name'        => 'Made in France',
                'description' => 'Produit fabriqué intégralement en France',
                'type'        => 'internal',
                'iconName'    => 'icon-made-in-france',
                'reference'   => 'ecolabel-made-in-france',
            ],
            [
                'name'        => 'Artisanal',
                'description' => 'Produit fabriqué de manière artisanale',
                'type'        => 'internal',
                'iconName'    => 'icon-artisanal',
                'reference'   => 'ecolabel-artisanal',
            ],
            [
                'name'        => 'Sans Plastique',
                'description' => 'Produit conçu sans aucun plastique',
                'type'        => 'internal',
                'iconName'    => 'icon-sans-plastique',
                'reference'   => 'ecolabel-sans-plastique',
            ],
            [
                'name'        => 'Biodégradable',
                'description' => 'Produit qui se décompose naturellement sans polluer',
                'type'        => 'internal',
                'iconName'    => 'icon-biodegradable',
                'reference'   => 'ecolabel-biodegradable',
            ],
            [
                'name'        => 'Matériaux Recyclés',
                'description' => 'Produit fabriqué à partir de matériaux recyclés',
                'type'        => 'internal',
                'iconName'    => 'icon-materiaux-recycles',
                'reference'   => 'ecolabel-materiaux-recycles',
            ],
            [
                'name'        => 'Consigné',
                'description' => 'Produit ou emballage retournable et réutilisable',
                'type'        => 'internal',
                'iconName'    => 'icon-consigne',
                'reference'   => 'ecolabel-consigne',
            ],
            [
                'name'        => 'Compostable',
                'description' => 'Produit pouvant être composté en fin de vie',
                'type'        => 'internal',
                'iconName'    => 'icon-compostable',
                'reference'   => 'ecolabel-compostable',
            ],
            [
                'name'        => 'Commerce Équitable',
                'description' => 'Produit issu d\'une filière de commerce équitable',
                'type'        => 'internal',
                'iconName'    => 'icon-commerce-equitable',
                'reference'   => 'ecolabel-commerce-equitable',
            ],
        ];

        foreach ($ecoLabels as $data) {
            $ecoLabel = new EcoLabel();
            $ecoLabel->setName($data['name']);
            $ecoLabel->setDescription($data['description']);
            $ecoLabel->setType($data['type']);
            $ecoLabel->setIconName($data['iconName']);

            $manager->persist($ecoLabel);
            $this->addReference($data['reference'], $ecoLabel);
        }

        $manager->flush();
    }
}
