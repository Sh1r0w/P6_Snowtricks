<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Connect;
use App\Form\ProfilType;
use App\Form\PassFormType;
use App\Services\ImgService;

class ProfilController extends AbstractController
{

    public function __construct(
        private ImgService $img, 
        private ManagerRegistry $doctrine,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager,
        ){

    }


    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request): Response {


        if (!is_null($this->getUser())){

        $id = $this->getUser()->getId();
        $user = $this->doctrine->getRepository(Connect::class)->find($id);

        $profilForm = $this->createForm(profilType::class, $user);
        $passForm = $this->createForm(PassFormType::class, $user);

        $profilForm->handleRequest($request);
        $passForm->handleRequest($request);

        $image = $profilForm->get('imguser')->getData();
        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            if($image != null) {
                $folder = $user->getUsername();
                $recordImg = $this->img->addImg($image, $folder);
                $user->setImguser($recordImg);
            };    
            $this->addFlash('success', 'Profil Modifié');      
        }
        
        if($passForm->isSubmitted() && $passForm->isValid()){
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $passForm->get('password')->getData()
                )
               
            );

            $this->addFlash('success','Mot de passe modifié');
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return $this->render('profil/index.html.twig', [
            'profilForm' => $profilForm->createView(),
            'passForm' => $passForm->createView(),
        ]);
     }else{
        $this->addFlash('error', 'Veuillez vous connecter');
        return $this->redirectToRoute('app_home');
     }
    }

    #[Route(path: 'deleteImgProfil/{id}', name: 'delete_img')]
    public function deleteImg(): Response {

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

}
