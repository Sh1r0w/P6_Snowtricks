<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

;

class CategoryFixtures extends Fixture
{
    private $count = 1;
    /**
     * The function loads categories into the object manager.
     * 
     * @param ObjectManager manager The `` parameter is an instance of the `ObjectManager`
     * class. It is responsible for managing the persistence of objects in the database. In this case,
     * it is used to persist the created categories.
     */
    public function load(ObjectManager $manager): void
    {
        $category = $this->createCategory('flips', $manager);
                  $this->createCategory('Grabs', $manager);
                  $this->createCategory('slides', $manager);
                  $this->createCategory('Old school', $manager);
        $manager->flush();

        
    }

    /**
     * The function creates a new category object with a given name and persists it using the provided
     * ObjectManager.
     * 
     * @param string name The name of the category that you want to create.
     * @param ObjectManager manager The "manager" parameter is an instance of the ObjectManager class.
     * It is responsible for managing the lifecycle of objects in the application, including persisting
     * and retrieving objects from the database.
     * 
     * @return Object an object of type "Categories".
     */
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
