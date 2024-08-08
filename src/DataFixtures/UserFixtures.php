<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('szymon.mastalerz@profitroom.com');
        $user->setRoles(['ROLE_ADMIN']);
        $encodedPassword = $this->passwordHasher->hashPassword($user, 'admin');
        $user->setPassword($encodedPassword);
        $manager->persist($user);
        $manager->flush();
    }
}
