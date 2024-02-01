<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

;

class CategoryFixtures extends Fixture
{
    private $count = 1;
    public function load(ObjectManager $manager): void
    {
        $category = $this->createCategory('flips', $manager);
                  $this->createCategory('Grabs', $manager);
                  $this->createCategory('slides', $manager);
                  $this->createCategory('Old school', $manager);
        $manager->flush();

        
    }

    public function createCategory(string $name, ObjectManager $manager): Object
    {
        $category = new Categories();
        $category->setCategory($name);
        $manager->persist($category);

        $this->setReference('cat-'. $this->count, $category);
        $this->count++;

        return $category;
    }
}
