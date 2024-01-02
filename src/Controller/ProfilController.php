<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Connect;
use App\Form\ProfilType;
use App\Form\PassFormType;
use App\Services\ImgService;

class ProfilController extends AbstractController
{
    private $user;

    public function __construct(
        private Security $security,
        private ImgService $img,
        private ManagerRegistry $doctrine,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $params,
    ) {
        $this->params = $params;
    }


    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request): Response
    {

        $id = $this->getUser()->getId();
        $this->user = $this->doctrine->getRepository(Connect::class)->find($id);
        $folder = $this->user->getUsername();

        if (!is_null($this->getUser())) {

            $profilForm = $this->createForm(profilType::class, $this->user);
            $passForm = $this->createForm(PassFormType::class, $this->user);

            $profilForm->handleRequest($request);
            $passForm->handleRequest($request);

            if ($profilForm->isSubmitted() && $profilForm->isValid()) {
                if ($profilForm->get('imguser')->getData() != false) {

                    if ($this->user->getImguser() != null) {
                        self::deleteImg();
                    }

                    $image = $profilForm->get('imguser')->getData();
                    $recordImg = $this->img->addImg($image, $this->user->getUsername());

                    $this->user->setImguser($recordImg);
                }
                ;

                if ($profilForm->get('username')->getData() != $folder) {
                    $path = $this->params->get('images_directory');
                    rename($path . $folder, $path . $profilForm->get('username')->getData());
                }
                $this->addFlash('success', 'Profil Modifié');
            }

            if ($passForm->isSubmitted() && $passForm->isValid()) {
                $this->user->setPassword(
                    $this->userPasswordHasher->hashPassword(
                        $this->user,
                        $passForm->get('password')->getData()
                    )

                );

                $this->addFlash('success', 'Mot de passe modifié');
            }

            $this->entityManager->persist($this->user);
            $this->entityManager->flush();

            return $this->render('profil/index.html.twig', [
                'profilForm' => $profilForm->createView(),
                'passForm' => $passForm->createView(),
            ]);
        } else {
            $this->addFlash('error', 'Veuillez vous connecter');
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route(path: 'deleteImgProfil', name: 'delete_img_profil')]
    public function deleteImg(): Response
    {
        if ($this->getUser()) {
            $id = $this->getUser()->getId();
            $this->user = $this->doctrine->getRepository(Connect::class)->find($id);
            $this->img->deleteProfil($this->user);

            $this->user->setImguser(null);
            $this->entityManager->persist($this->user);
            $this->entityManager->flush();
        } else {
            $this->addFlash('error', 'Veuillez vous connecter');
        }
        return $this->redirectToRoute('app_profil');
    }


}
