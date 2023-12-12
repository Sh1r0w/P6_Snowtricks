<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use App\Entity\Figure;
use App\Entity\Connect;
use App\Entity\Categories;
use App\Entity\Image;
use App\Form\FigureFormType;
use App\Services\ImgService;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $figures = $doctrine->getRepository(Figure::class)->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'figures' => $figures,
        ]);
    }

    #[Route('/addFigure', name: 'add_figure')]
    public function add(
        EntityManagerInterface $entityManager,
        Request $request,
        ImgService $imgService,
    ): Response {

        $figure = new Figure();

        $figureForm = $this->createForm(FigureFormType::class, $figure);

        $figureForm->handleRequest($request);

        if ($figureForm->isSubmitted() && $figureForm->isValid()) {
            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($figure->getTitle());

            $images = $figureForm->get('image')->getData();

            foreach ($images as $image) {
                $folder = $figure->getTitle();

                $fichier = $imgService->addImg($image, $folder, 300, 300);

                $img = new Image();
                $img->setName($fichier);
                $figure->addImage($img);

            }


            $user = $this->getUser()->getId();

            $connect = $entityManager->getRepository(Connect::class)->findOneBy(['id' => $user]);
            $figure->setTitle($figure->getTitle())
                ->setDescription($figure->getDescription())
                ->setConnect($connect)
                ->setDateTimeAdd(new \DateTime())
                /*->setVideo($figure->getVideos())*/
                ->setSlug($slug);
            $entityManager->persist($figure);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('forms/addFigure.html.twig', [
            'figureForm' => $figureForm->createView()
        ]);
    }
}
