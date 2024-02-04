<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
;

class VideoFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $this->createVideo('https://www.youtube.com/embed/k6aOWf0LDcQ', $this->getReference('fig-1'), $manager);
        $this->createVideo('https://www.youtube.com/embed/KEdFwJ4SWq4', $this->getReference('fig-2'), $manager);
        $this->createVideo('https://www.youtube.com/embed/4AlDWWsprZM', $this->getReference('fig-3'), $manager);
        $this->createVideo('https://www.youtube.com/embed/xXCCGYqAWqI', $this->getReference('fig-4'), $manager);
        $this->createVideo('https://www.youtube.com/embed/YAElDqyD-3I', $this->getReference('fig-5'), $manager);
        $this->createVideo('https://www.youtube.com/embed/80pI61w_qtk', $this->getReference('fig-6'), $manager);
        $this->createVideo('https://www.youtube.com/embed/hW_RhD0D-Ew', $this->getReference('fig-7'), $manager);
        $this->createVideo('https://www.youtube.com/embed/N3ddt_yoxts', $this->getReference('fig-8'), $manager);
        $this->createVideo('https://www.youtube.com/embed/HRNXjMBakwM', $this->getReference('fig-9'), $manager);
        $this->createVideo('https://www.youtube.com/embed/jH76540wSqU', $this->getReference('fig-10'), $manager);

        $manager->flush();
    }

    public function createVideo(string $name, Object $fig, ObjectManager $manager)
    {
        $video = new Video();
        $video->setName($name);
        $video->setFigure($fig);
        $manager->persist($video);
    }

    public function getDependencies(): Array
    {
        return [
            FigureFixtures::class
        ];
    }
}
