<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Figure;
use App\Entity\Categories;
use App\Entity\Profil;
use App\Form\FigureFormType;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        
        $figures = $doctrine->getRepository(Figure::class)->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/addFigure', name: 'add_figure')]
    public function add(EntityManagerInterface $entityManager, Request $request): Response
    {

        $figure = new Figure();

        $figureForm = $this->createForm(FigureFormType::class, $figure);

        $figureForm->handleRequest($request);
        
        if ($figureForm->isSubmitted() && $figureForm->isValid()) { 
            $user = $this->getUser()->getId();
            
            $profil = $entityManager->getRepository(Profil::class)->findOneBy(['id_connect' => $user]);
            $figure->setTitle($figure->getTitle())
                ->setDescription($figure->getDescription())
                ->setProfil($profil)
                ->setMedia($figure->getMedia());
        $entityManager->persist($figure);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
        }

        return $this->render('forms/addFigure.html.twig',[
            'figureForm' => $figureForm->createView()
        ]);
    }
}
