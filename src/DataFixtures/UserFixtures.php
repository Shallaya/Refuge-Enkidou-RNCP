<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    // on injecte le service de hashage de mot de passe dans le constructeur
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email' => 'admin@example.com',
                // 'username' => 'Admin',
                'roles' => ['ROLE_ADMIN'],
                'password' => 'admin',
                'isVerified' => true,
                'isActive' => true,
            ],
            [
                'email' => 'kanami.tono@example.com',
                // 'username' => 'KanamiTono',
                'roles' => ['ROLE_USER'],
                'password' => '123',
                'isVerified' => true,
                'isActive' => true,
            ],
            [
                'email' => 'miku.kabato@example.com',
                // 'username' => 'MikuKabato',
                'roles' => ['ROLE_USER'],
                'password' => '123',
                'isVerified' => true,
                'isActive' => true,
            ],
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            // $user->setUserName($data['username']);
            $user->setRoles($data['roles']);
            // on hash le mot de passe avant de le stocker en base de données
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
            $user->setIsVerified(true);
            $user->setIsActive(true);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
