<?php

namespace App\DataFixtures;

use App\Entity\PetType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PetTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $petTypes = [
            ['name' => 'Chien', 'reference' => 'pettype-chien'],
            ['name' => 'Chat', 'reference' => 'pettype-chat'],
        ];

        foreach ($petTypes as $data) {
            $petType = new PetType();
            $petType->setName($data['name']);

            $manager->persist($petType);
            $this->addReference($data['reference'], $petType);
        }

        $manager->flush();
    }
}
