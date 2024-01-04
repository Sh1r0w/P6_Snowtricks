<?php

namespace App\Controller;

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

    #[Route('/figure/{slug}', name: 'detail_figure')]
    public function index(
        Request $request,
        ?Figure $figure,
        ): Response
    {

        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);



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

        $getComment = $this->entityManager->getRepository(Comment::class)->findBy(['figure' => $figure->getId()]);

        return $this->render('figure/index.html.twig', [
            'commentForm' => $commentForm->createView(),
            'figure' => $figure,
            'getComment' => $getComment,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_figure')]
    public function delete( ?Figure $figure ): Response {

        //self::unknow($figure);
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
                    $control = explode("/", $videos);
                    if($control[3] != 'embed')
                    {
                        $addEmbed = str_replace($control[2], $control[2] . '/embed', $videos);
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
    
    #[route(path:'deleteVideo/{figure}/{id}', name: 'delete_video')]
    public function deleteVideo(Figure $figure, Video $video): Response
{
    $this->entityManager->remove($video);
    $this->entityManager->flush();
    
   $this->addFlash('success','Video supprimée avec succès');

   return $this->redirectToRoute('update_figure', ['figure' => $figure->getId()]);
}

}
