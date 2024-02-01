<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i <= 155; $i++){
        $comment = new Comment();
        $comment->setComment($faker->text());
        $comment->setConnect($this->getReference('user-1'));
        $comment->setDate(new \DateTime);
        $comment->setFigure($this->getReference('fig-'. rand(1, 10)));
        $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FigureFixtures::class
        ];
    }
}
