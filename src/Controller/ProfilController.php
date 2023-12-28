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

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        ManagerRegistry $doctrine,
    ): Response {


        if (!is_null($this->getUser())){
        $id = $this->getUser()->getId();
        $user = $doctrine->getRepository(Connect::class)->find($id);
        $profilForm = $this->createForm(profilType::class, $user);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            if($user->getPassword() != $profilForm->getData()['password']){
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $profilForm->get('password')->getData()
                )
                );
            }
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('profil/index.html.twig', [
            'profilForm' => $profilForm->createView(),
        ]);
     }else{
        $this->addFlash('error', 'Veuillez vous connecter');
        return $this->redirectToRoute('app_home');
     }
    }

}
