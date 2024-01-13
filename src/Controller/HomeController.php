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
use App\Entity\Video;
use App\Entity\Image;
use App\Form\FigureFormType;
use App\Services\ImgService;

class HomeController extends AbstractController
{
    /**
     * The index function retrieves all figures from the database using Doctrine and renders the
     * home/index.html.twig template with the figures as a variable.
     * 
     * @param ManagerRegistry doctrine The `` parameter is an instance of the
     * `ManagerRegistry` class. It is used to manage and retrieve entity managers in Doctrine. In this
     * case, it is used to retrieve the entity manager for the `Figure` entity.
     * 
     * @return Response a Response object.
     */
    #[Route('/', methods:['GET'], name: 'app_home')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $figures = $doctrine->getRepository(Figure::class)->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'figures' => $figures,
        ]);
    }

    /**
     * This PHP function handles the form submission for adding a new figure, including uploading
     * images and videos, and saving the figure data to the database.
     * 
     * @param EntityManagerInterface entityManager The `` parameter is an instance of the
     * `EntityManagerInterface` class, which is responsible for managing the persistence of objects in
     * the database. It is used to interact with the database and perform operations such as persisting
     * new objects, updating existing objects, and deleting objects.
     * @param Request request The `` parameter is an instance of the `Request` class, which
     * represents an HTTP request. It contains information about the request, such as the request
     * method, headers, and request data.
     * @param ImgService imgService ImgService is a service that handles image manipulation and
     * storage. It is used to add images to the Figure entity.
     * 
     * @return Response The code is returning a Response object.
     */
    #[Route('/addFigure', methods: ['GET', 'POST'], name: 'add_figure')]
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
            $videos = $figureForm->get('videos')->getData();

            foreach ($images as $image) {
                $folder = $figure->getTitle();

                $fichier = $imgService->addImg($image, $folder, 300, 300);

                $img = new Image();
                $img->setName($fichier);
                $figure->addImage($img);

            }
            if($videos){
                $video = new Video();
                $video->setName($videos);
                $figure->addVideo($video);
            }
            
            
            $user = $this->getUser()->getId();

            $connect = $entityManager->getRepository(Connect::class)->findOneBy(['id' => $user]);
            $figure->setTitle($figure->getTitle())
                ->setDescription($figure->getDescription())
                ->setConnect($connect)
                ->setDateTimeAdd(new \DateTime())
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
