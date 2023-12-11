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
use App\Entity\Comment;
use App\Services\ImgService;
use App\Form\CommentType;

class FigureController extends AbstractController
{
    #[Route('/figure/{slug}', name: 'detail_figure')]
    public function index(Figure $figure, Request $request): Response
    {

        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()){
            $comment->setCommentaire($comment->getComment());
        }

        return $this->render('figure/index.html.twig', [
            'commentForm' => $commentForm->createView(),
            'figure' => $figure,

        ]);
    }

    #[Route('/delete/{id}', name: 'delete_figure')]
    public function delete(EntityManagerInterface $entityManager, int $id, ImgService $img): Response
    {
        $figure = $entityManager->getRepository(Figure::class)->find($id);

        if (!$figure){
            $this->createNotFoundException(
                'Pas de figure avec l\' '.$id
            );
        }

        $img->delete($figure->getImage()[0]->getName());

        $entityManager->remove($figure);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
