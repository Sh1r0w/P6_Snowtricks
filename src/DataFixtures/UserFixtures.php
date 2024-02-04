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
    /**
     * The function is a constructor that takes two dependencies, UserPasswordHasherInterface and
     * SluggerInterface, and assigns them to private properties.
     */
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
        ){}

    /**
     * The function creates and persists a new admin user with predefined email, username, password,
     * roles, and verification status.
     * 
     * @param ObjectManager manager The `` parameter is an instance of the `ObjectManager`
     * class. It is responsible for managing the persistence of objects in the database. In this
     * context, it is used to persist the `` object and flush the changes to the database.
     */
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
