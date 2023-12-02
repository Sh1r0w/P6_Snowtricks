<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Figure;
use App\Entity\Categories;
use App\Entity\Profil;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {

        $figures = $doctrine->getRepository(Figure::class)->findAll();
        
        

        $figure = new Figure();
        $categories = $entityManager->getRepository(Categories::class)->find(1);
        $profil = $entityManager->getRepository(Profil::class)->find(1);
        $figure->setTitle('Test')
                ->setDescription('Premier Test')
                ->setCategories($categories)
                ->setProfil($profil)
                ->setMedia('');
        $entityManager->persist($figure);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            echo 'Exception lors du flush : ', $e->getMessage(), "\n";
        }

        return $this->render('home.twig', [
            'controller_name' => 'HomeController',
            'figures' => $figures,
        ]);
    }
}
