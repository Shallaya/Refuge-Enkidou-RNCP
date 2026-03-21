<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\PetType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $petTypes = [
            'chien' => $this->getReference('pettype-chien', PetType::class),
            'chat'  => $this->getReference('pettype-chat', PetType::class),
        ];

        $menu = [

            'chien' => [
                'ALIMENTATION ÉCORESPONSABLE' => [
                    'children' => [
                        'Pâtées & Nourriture humide',
                        'Croquettes écoresponsables',
                        'Friandises & Compléments',
                        'Accessoires',
                    ]
                ],
                'HYGIÈNE & TOILETTAGE' => [
                    'children' => [
                        'Produits de soin',
                        'Toilettage',
                    ]
                ],
                'PROPRETÉ' => [
                    'children' => [
                        'Accessoires',
                    ]
                ],
                'COUCHAGE & CONFORT' => [
                    'children' => [
                        'Niches',
                        'Paniers & Lits',
                        'Confort & Détente',
                    ]
                ],
                'PROMENADE & TRANSPORT' => [
                    'children' => [
                        'Laisses, colliers & harnais',
                        'Mobilité',
                        'Accessoires',
                    ]
                ],
                'JOUETS ÉCOLOGIQUES' => [
                    'children' => [
                        'Jouets naturels',
                        'Jouets recyclés',
                        'Jeux éducatifs',
                    ]
                ],
            ],

            'chat' => [
                'ALIMENTATION ÉCORESPONSABLE' => [
                    'children' => [
                        'Pâtées & Nourriture humide',
                        'Croquettes écoresponsables',
                        'Friandises & Compléments',
                        'Accessoires',
                    ]
                ],
                'HYGIÈNE & TOILETTAGE' => [
                    'children' => [
                        'Produits de soin',
                        'Toilettage',
                    ]
                ],
                'LITIÈRE & PROPRETÉ' => [
                    'children' => [
                        'Litière',
                        'Accessoires',
                    ]
                ],
                'COUCHAGE & CONFORT' => [
                    'children' => [
                        'Paniers & lits',
                        'Confort & détente',
                    ]
                ],
                'PROMENADE & TRANSPORT' => [
                    'children' => [
                        'Mobilité',
                        'Harnais & Laisses',
                        'Accessoires',
                    ]
                ],
                'JOUETS ÉCOLOGIIQUES' => [
                    'children' => [
                        'Jouets naturels',
                        'Jouets recyclés',
                        'Jeux éducatifs',
                    ]
                ],
            ],
        ];

        foreach ($menu as $typeKey => $categories) {
            foreach ($categories as $parentName => $data) {

                // Catégorie parente
                $parent = new Category();
                $parent->setName($parentName);
                $parent->setPetType($petTypes[$typeKey]);

                $manager->persist($parent);

                // Sous-catégories
                foreach ($data['children'] as $childName) {

                    $child = new Category();
                    $child->setName($childName);
                    $child->setPetType($petTypes[$typeKey]);
                    $child->setParent($parent);

                    $manager->persist($child);
                }
            }
        }

        $manager->flush();
    }

    // Cette fixture dépend de PetTypeFixtures pour les références. Il faut s'assurer que PetTypeFixtures est chargé avant.
    public function getDependencies(): array
    {
        return [
            PetTypeFixtures::class,
        ];
    }
}