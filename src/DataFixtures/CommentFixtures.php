<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * This function loads 155 comments into the database with random text, a user reference, a current
     * date, and a figure reference.
     * 
     * @param ObjectManager manager The `` parameter is an instance of the `ObjectManager`
     * class. It is responsible for managing the persistence of objects in the database. It provides
     * methods like `persist()` to save new objects and `flush()` to persist all changes to the
     * database.
     */
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

    /**
     * The function returns an array of dependencies for a PHP class.
     * 
     * @return array An array containing the class FigureFixtures.
     */
    public function getDependencies(): array
    {
        return [
            FigureFixtures::class
        ];
    }
}
