<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ImgFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createImg('1', $this->getReference('fig-1'), $manager);
        $this->createImg('2', $this->getReference('fig-2'), $manager);
        $this->createImg('3', $this->getReference('fig-3'), $manager);
        $this->createImg('4', $this->getReference('fig-4'), $manager);
        $this->createImg('5', $this->getReference('fig-5'), $manager);
        $this->createImg('6', $this->getReference('fig-6'), $manager);
        $this->createImg('7', $this->getReference('fig-7'), $manager);
        $this->createImg('8', $this->getReference('fig-8'), $manager);
        $this->createImg('9', $this->getReference('fig-9'), $manager);
        $this->createImg('10', $this->getReference('fig-10'), $manager);
        $manager->flush();
    }

    public function createImg(string $name, Object $ref, ObjectManager $manager): void {
        $image = new Image();
            $image->setName($name.'.webp');
            $image->setFigure($ref);
            $manager->persist($image);
    }

    public function getDependencies(): Array 
    {
        return [
            FigureFixtures::class
        ];
    }
}
