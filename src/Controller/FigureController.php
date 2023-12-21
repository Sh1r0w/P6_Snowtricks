<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use App\Entity\Image;
use App\Entity\Figure;
use App\Entity\Connect;
use App\Entity\Comment;
use App\Services\ImgService;
use App\Form\CommentType;
use App\Controller\HomeController;
use App\Form\UpdateFigureType;

class FigureController extends AbstractController
{
    #[Route('/figure/{slug}', name: 'detail_figure')]
    public function index(Figure $figure, EntityManagerInterface $entityManager, Request $request): Response
    {

        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);



        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $user = $this->getUser()->getId();
            $connect = $entityManager->getRepository(Connect::class)->findOneBy(['id' => $user]);
            $figure = $entityManager->getRepository(Figure::class)->findOneBy(['slug' => $figure->getSlug()]);

            $comment->setComment($comment->getComment())
                ->setConnect($connect)
                ->setFigure($figure);

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Commentaire envoyé'
            );

            return $this->redirectToRoute('detail_figure', array('slug' => $figure->getSlug()));

        }

        $getComment = $entityManager->getRepository(Comment::class)->findBy(['figure' => $figure->getId()]);

        return $this->render('figure/index.html.twig', [
            'commentForm' => $commentForm->createView(),
            'figure' => $figure,
            'getComment' => $getComment,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_figure')]
    public function delete(
        EntityManagerInterface $entityManager,
        Figure $figure,
        ImgService $img
    ): Response {

        //self::unknow($figure);

        if ($figure->getImage()[0]) {
            $img->delete($figure->getImage()[0]->getName(), $figure->getTitle());
        }

        $entityManager->remove($figure);
        $entityManager->flush();

        $this->addFlash('success', 'Tricks supprimée avec succès');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/update/{figure}', name: 'update_figure')]
    public function update(
        EntityManagerInterface $entityManager,
        Figure $figure,
        Request $request,
        ImgService $img,
        HomeController $add,
        UpdateFigureType $updateForm,
    ): Response {


        $updateForm = $this->createForm(UpdateFigureType::class, $figure);
        $updateForm->handleRequest($request);

        //self::unknow($figure);

        if ($updateForm->isSubmitted() && $updateForm->isValid()) {
            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($figure->getTitle());

            $images = $updateForm->get('image')->getData();

            foreach ($images as $image) {
                $folder = $figure->getTitle();

                $fichier = $img->addImg($image, $folder, 300, 300);

                $img = new Image();
                $img->setName($fichier);
                $figure->addImage($img);
            }


            $figure->setTitle($figure->getTitle())
                ->setDescription($figure->getDescription())
                ->setDateUpdate(new \DateTime())
                ->setSlug($slug);
            $entityManager->persist($figure);
            $entityManager->flush();

        }

        return $this->render('figure/update.html.twig', [
            'figure' => $figure,
            'updateForm' => $updateForm,
        ]);
    }

    #[Route(path: 'deleteImg/{figure}/{id}', name: 'delete_img')]
    public function deleteImg(
        ImgService $img,
        Image $image,
        Figure $figure,
        EntityManagerInterface $entityManager,
    ): Response {

        
        $entityManager->remove($image);
        $entityManager->flush();
        
        $success = $img->deleteOne($image, $figure->getTitle());

        if ($success) {
            $this->addFlash('success', 'Image Supprimée avec succès');
        } else {
            $this->addFlash('error', 'Echec de la suppresion');
        }

        return $this->redirectToRoute('update_figure', [ 'figure' => $figure->getId() ]);

    }

    public function unknow(
        $figure
    ): void {
        if (!$figure) {
            $this->createNotFoundException(
                'Pas de figure avec l\' ' . $figure->getId()
            );
        }
    }
}
