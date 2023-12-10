<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use App\Entity\Figure;

class FigureController extends AbstractController
{
    #[Route('/figure/{slug}', name: 'detail_figure')]
    public function index(Figure $figure): Response
    {
        return $this->render('figure/index.html.twig', [
            'controller_name' => 'FigureController',
            'figure' => $figure,

        ]);
    }

    #[Route('/delete/{id}', name: 'delete_figure')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $figure = $entityManager->getRepository(Figure::class)->find($id);

        if (!$figure){
            $this->createNotFoundException(
                'Pas de figure avec l\' '.$id
            );
        }

        $entityManager->remove($figure);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
