<?php

namespace App\DataFixtures;

use App\Entity\Connect;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
;

class UserFixtures extends Fixture
{
    private $count = 1;
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
        ){}

    public function load(ObjectManager $manager): void
    {
        $admin = new Connect();
        $admin->setEmail('admin@snowtricks.fr');
        $admin->setUsername('Admin');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified('1');

        $manager->persist($admin);
        $manager->flush();

        $this->setReference('user-'. $this->count, $admin);
        $this->count++;
    }
}
