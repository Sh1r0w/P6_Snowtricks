<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Image;
use App\Entity\Video;
use App\Entity\Figure;
use App\Entity\Connect;
use App\Entity\Comment;
use App\Services\ImgService;
use App\Form\CommentType;
use App\Controller\HomeController;
use App\Form\UpdateFigureType;

class FigureController extends AbstractController
{
    /**
     * The function is a constructor that initializes several dependencies for a PHP class.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ImgService $img,
        private HomeController $add,
        private UpdateFigureType $updateForm,
        private ParameterBagInterface $params,
        )
    {
        $this->params = $params;
    }

    /**
     * This PHP function handles the display and submission of comments for a specific figure,
     * including pagination and form validation.
     * 
     * @param Request request The `` parameter is an instance of the `Request` class, which
     * represents an HTTP request. It contains information about the request, such as the request
     * method, headers, query parameters, and request body.
     * @param figure The "figure" parameter is a nullable instance of the Figure class. It represents a
     * specific figure object that is being requested. If the figure is not found, it will be null.
     * @param commentRepository The `` parameter is an instance of the
     * `CommentRepository` class. It is used to retrieve and manipulate comment data from the database.
     * 
     * @return Response a Response object.
     */
    #[Route('/figure/{slug}', name: 'detail_figure')]
    public function index(
        Request $request,
        ?Figure $figure,
        ?CommentRepository $commentRepository,
        ): Response
    {

        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);

        $page = $request->query->getInt('page',1);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $user = $this->getUser()->getId();
            $connect = $this->entityManager->getRepository(Connect::class)->findOneBy(['id' => $user]);
            $figure = $this->entityManager->getRepository(Figure::class)->findOneBy(['slug' => $figure->getSlug()]);

            $comment->setComment($comment->getComment())
                ->setConnect($connect)
                ->setFigure($figure);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                'Commentaire envoyé'
            );

            return $this->redirectToRoute('detail_figure', array('slug' => $figure->getSlug()));

        }

        $getComment =  $commentRepository->findCommentPaginated($page, $figure->getId(), 10);


        return $this->render('figure/index.html.twig', [
            'commentForm' => $commentForm->createView(),
            'figure' => $figure,
            'getComment' => $getComment,
        ]);
    }

    /**
     * This PHP function deletes a figure and its associated image from the database, and redirects the
     * user to the home page.
     * 
     * @param figure The parameter "figure" is of type "Figure" and is nullable. It is used to
     * represent a figure object that will be deleted.
     * 
     * @return Response a Response object.
     */
    #[Route('/delete/{id}', name: 'delete_figure')]
    public function delete( ?Figure $figure ): Response {

        if (!is_null($this->getUser())) {
            if ($figure->getImage()[0]) {
                $this->img->delete($figure->getImage()[0]->getName(), $figure->getTitle());
            }

            $this->entityManager->remove($figure);
            $this->entityManager->flush();

            $this->addFlash('success', 'Tricks supprimée avec succès');


        } else {
            $this->addFlash('error', ('Veuillez vous connecter'));
        }
        return $this->redirectToRoute('app_home');
    }

    //#[Route('/update/{figure}', name: 'update_figure')]
    /**
     * This PHP function updates a figure entity with new data, including images and videos, and saves
     * it to the database.
     * 
     * @param Request request The `` parameter is an instance of the `Request` class, which
     * represents an HTTP request. It contains information about the request, such as the request
     * method, headers, and request data.
     * @param figure The "figure" parameter is a placeholder for the figure object that is being
     * updated. It is passed as a route parameter in the URL. The figure object is retrieved from the
     * database based on the provided identifier.
     * 
     * @return Response a Response object.
     */
    #[Route('/update/{figure}', name: 'update_figure')]
    public function update(
        Request $request,
        ?Figure $figure
    ): Response {

        if (!is_null($this->getUser())) {
            $oldFolder = $figure->getTitle();
            $updateForm = $this->createForm(UpdateFigureType::class, $figure);
            $updateForm->handleRequest($request);

            if ($updateForm->isSubmitted() && $updateForm->isValid()) {
                $slugger = new AsciiSlugger();
                $slug = $slugger->slug($figure->getTitle());

                $images = $updateForm->get('image')->getData();
                $videos = $updateForm->get('videos')->getData();
                $folder = $figure->getTitle();

                if($oldFolder != $updateForm->get('title')->getData()) {
                    $this->img->renameFolder($oldFolder, $updateForm->get('title')->getData());
                }
                
                foreach ($images as $image) {
                    
                    $fichier = $this->img->addImg($image, $folder, 300, 300);
                    $picture = new Image();
                    $picture->setName($fichier);
                    $figure->addImage($picture);
                }

                if($videos){
                    $control = preg_split("/[\/=]/", $videos);
                    unset($control[array_search("watch?v", $control)]);


                    if(!array_search("embed", $control))
                    {
                        $addEmbed = 'https://'. $control[2] . '/embed/' . end($control);

                        $video = new Video();
                        $video->setName($addEmbed);
                        $figure->addVideo($video);
                    }else{
                    $video = new Video();
                    $video->setName($videos);
                    $figure->addVideo($video);
                    }
                }


                $figure->setTitle($figure->getTitle())
                    ->setDescription($figure->getDescription())
                    ->setDateUpdate(new \DateTime())
                    ->setSlug($slug);
                $this->entityManager->persist($figure);
                $this->entityManager->flush();
                

            }
        } else {

            $this->addFlash('error', 'Veuillez vous connecter');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('figure/update.html.twig', [
            'figure' => $figure,
            'updateForm' => $updateForm,
        ]);
    }

    /**
     * This PHP function deletes an image, removes it from the database, and displays a success or
     * error message.
     * 
     * @param Image image The `` parameter is an instance of the `Image` class, which represents
     * an image entity in the database. It is used to identify the specific image that needs to be
     * deleted.
     * @param figure The "figure" parameter is an optional parameter of type Figure. It represents the
     * figure object associated with the image being deleted.
     * 
     * @return Response a Response object.
     */
    #[Route(path: 'deleteImg/{figure}/{id}', name: 'delete_img')]
    public function deleteImg(
        Image $image,
        ?Figure $figure
        ): Response {

        $this->entityManager->remove($image);
        $this->entityManager->flush();

        $success = $this->img->deleteOne($image, $figure->getTitle());

        if ($success) {
            $this->addFlash('success', 'Image Supprimée avec succès');
        } else {
            $this->addFlash('error', 'Echec de la suppresion');
        }

        return $this->redirectToRoute('update_figure', ['figure' => $figure->getId()]);

    }
    
    /**
     * This PHP function deletes a video and redirects to the update figure page with a success flash
     * message.
     * 
     * @param Figure figure The "figure" parameter is an instance of the Figure class. It is used to
     * identify the figure for which the video needs to be deleted.
     * @param Video video The "video" parameter is an instance of the Video class. It represents the
     * video that needs to be deleted.
     * 
     * @return Response a Response object.
     */
    #[route(path:'deleteVideo/{figure}/{id}', name: 'delete_video')]
    public function deleteVideo(Figure $figure, Video $video): Response
{
    $this->entityManager->remove($video);
    $this->entityManager->flush();
    
   $this->addFlash('success','Video supprimée avec succès');

   return $this->redirectToRoute('update_figure', ['figure' => $figure->getId()]);
}

}
