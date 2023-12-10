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
}
