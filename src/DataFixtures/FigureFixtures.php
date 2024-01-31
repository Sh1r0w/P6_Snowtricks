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
    public function __construct(
       private SluggerInterface $slugger
    ){}
    public function load(ObjectManager $manager): void
    {
        
        $this->createTricks('Mute', $this->getReference('cat-2'), $manager);
        $this->createTricks('sad ou melancholie', $this->getReference('cat-2'), $manager);
        $this->createTricks('indy', $this->getReference('cat-2'), $manager);
        $this->createTricks('stalefish ', $this->getReference('cat-2'), $manager);
        $this->createTricks('tail grab', $this->getReference('cat-2'), $manager);
        $this->createTricks('front flips', $this->getReference('cat-1'), $manager);
        $this->createTricks('back flips', $this->getReference('cat-1'), $manager);
        $this->createTricks('nose slide', $this->getReference('cat-3'), $manager);
        $this->createTricks('tail slide', $this->getReference('cat-3'), $manager);
        $this->createTricks('Japan air, rocket air', $this->getReference('cat-4'), $manager);


        $manager->flush();
    }

    public function createTricks(string $name, $category, ObjectManager $manager){

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

    public function getDependencies(): Array
    {
        return [
            UserFixtures::class
        ];
    }
}
