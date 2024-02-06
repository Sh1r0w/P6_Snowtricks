<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class FigureFixtures extends Fixture implements DependentFixtureInterface
{
    
    private $count = 1;
    /**
     * The function is a constructor that takes a SluggerInterface object as a parameter and assigns it
     * to the private property .
     */
    public function __construct(
       private SluggerInterface $slugger
    ){}
    /**
     * The function creates and saves various tricks with their corresponding categories using an
     * ObjectManager.
     * 
     * @param ObjectManager manager The `` parameter is an instance of the `ObjectManager`
     * class. It is responsible for managing the persistence of objects in the database. In this case,
     * it is used to persist the created tricks to the database.
     */
    public function load(ObjectManager $manager): void
    {
        
        $this->createTricks('Mute', $this->getReference('cat-2'), $manager);
        $this->createTricks('sad ou melancholie', $this->getReference('cat-2'), $manager);
        $this->createTricks('indy', $this->getReference('cat-2'), $manager);
        $this->createTricks('stalefish', $this->getReference('cat-2'), $manager);
        $this->createTricks('tail grab', $this->getReference('cat-2'), $manager);
        $this->createTricks('front flips', $this->getReference('cat-1'), $manager);
        $this->createTricks('back flips', $this->getReference('cat-1'), $manager);
        $this->createTricks('nose slide', $this->getReference('cat-3'), $manager);
        $this->createTricks('tail slide', $this->getReference('cat-3'), $manager);
        $this->createTricks('Japan air, rocket air', $this->getReference('cat-4'), $manager);


        $manager->flush();
    }

    /**
     * The function creates a new trick with a given name, category, and manager, and returns the
     * created trick.
     * 
     * @param string name The name of the trick.
     * @param Object category The  parameter is an object representing the category of the
     * trick. It is passed as an argument to the createTricks() function.
     * @param ObjectManager manager The `` parameter is an instance of the `ObjectManager`
     * class, which is responsible for managing the persistence of objects in the database. It is
     * typically used to persist the newly created `Figure` object using the `persist()` method and to
     * flush the changes to the database using the `flush
     * 
     * @return Object an object of type Figure.
     */
    public function createTricks(string $name,Object $category, ObjectManager $manager): Object
    {

        $faker = Faker\Factory::create('fr_FR');

        $tricks = new Figure();
        $tricks->setTitle($name);
        $tricks->setSlug($this->slugger->slug($tricks->getTitle())->lower());
        $tricks->setDescription($faker->paragraph(3, true));
        $tricks->setDatetimeAdd(new \DateTime());
        $tricks->setCategories($category);
        $tricks->setConnect($this->getReference('user-'. 1));

        $manager->persist($tricks);

        $this->setReference('fig-'. $this->count, $tricks);
        $this->count++;

        return $tricks;
    }

    /**
     * The function returns an array of dependencies, specifically the UserFixtures class.
     * 
     * @return Array An array containing the class UserFixtures.
     */
    public function getDependencies(): Array
    {
        return [
            UserFixtures::class
        ];
    }
}
