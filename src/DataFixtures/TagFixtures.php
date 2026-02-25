<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tags = [
            [
                'name'        => 'Nouveau',
                'description' => 'Produit récemment ajouté à notre catalogue',
                'iconName'    => 'icon-new',
                'reference'   => 'tag-nouveau',
            ],
            [
                'name'        => 'Best-seller',
                'description' => 'Produit plébiscité par notre communauté',
                'iconName'    => 'icon-bestseller',
                'reference'   => 'tag-bestseller',
            ],
            [
                'name'        => 'Promo',
                'description' => 'Produit actuellement en promotion',
                'iconName'    => 'icon-promo',
                'reference'   => 'tag-promo',
            ],
        ];

        foreach ($tags as $data) {
            $tag = new Tag();
            $tag->setName($data['name']);
            $tag->setDescription($data['description']);
            $tag->setIconName($data['iconName']);

            $manager->persist($tag);
            $this->addReference($data['reference'], $tag);
        }

        $manager->flush();
    }
}
