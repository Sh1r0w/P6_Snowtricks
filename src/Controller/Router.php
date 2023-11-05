<?php 

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/* The Router class extends AbstractController and defines a home() method that returns a response by
rendering the 'home.twig' template. */
class Router extends AbstractController
{

     #[Route('/')]
     public function home(): response
     {
        return $this->render('home.twig');
     }
}