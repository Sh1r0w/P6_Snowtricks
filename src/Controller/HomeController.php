<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Figure;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {

        $figure = $doctrine->getRepository(Figure::class);
        $figures = $figure->findAll();
        
        /*$figure = new Figure();
        $figure->setTitle(title: 'Test')
                ->setDescription(description: 'Premier Test')
                ->setCategory(category: '2')
                ->setMedia(media: '');
        $entityManager->persist($figure);
        $entityManager->flush();*/
        return $this->render('home.twig', [
            'controller_name' => 'HomeController',
            'figures' => $figures,
        ]);
    }
}
