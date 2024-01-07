<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use App\Entity\Connect;
use App\Services\JWTService;
use App\Services\MailService;
use App\Form\PassFormType;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private JWTService $jwt,
        private MailService $mail,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $userPasswordHasher,

    ) {
    }

    #[Route('/reset/password', name: 'app_reset_password')]
    public function index(
        Request $request,
    ): Response {
        $passForm = $this->createForm(ResetPasswordType::class);
        $passForm->handleRequest($request);

        if ($passForm->isSubmitted() && $passForm->isValid()) {

            $getMail = $passForm->get('email')->getData();
            $user = $this->doctrine->getRepository(Connect::class)->findOneBy(['email' => $getMail]);
            if ($user) {
                $header = [
                    'typ' => 'JWT',
                    'alg' => 'HS256'
                ];

                $payload = [
                    'user_id' => $user->getId(),
                ];

                $token = $this->jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

                $user->setResetToken($token);
                $this->em->persist($user);
                $this->em->flush();

                $this->mail->send(
                    'no-reply@snowtricks.com',
                    $user->getEmail(),
                    'Reinitialisation du mot de passe',
                    'resetPassword',
                    compact('user', 'token')
                );

                $this->addFlash('success','Mail Envoyé');
            } else {
                $this->addFlash('error','Utilisateur inconnue');
            }
            
        }
        return $this->render('reset_password/index.html.twig', [
                'pass' => $passForm->createView(),
            ]);
        
    }

    #[Route('reset/{token}', name: 'app_reset_step_2')]
    public function resetPassword2(
        $token,
        request $request
    ): Response {
        if ($token && !$this->jwt->isExpired($token)) {
            $user = $this->doctrine->getRepository(Connect::class)->findOneBy(['resetToken' => $token]);
            if ($user) {
                $passForm = $this->createForm(PassFormType::class, $user);
                $passForm->handleRequest($request);
                if ($passForm->isSubmitted() && $passForm->isValid()) {

                    $user->setPassword(
                        $this->userPasswordHasher->hashPassword(
                            $user,
                            $passForm->get('password')->getData()
                        )
                    )
                        ->setResetToken(null);
                        
                    $this->em->persist($user);
                    $this->em->flush();

                    $this->addFlash('success', 'Mot de passe modifié');
                }

                return $this->render('reset_password/reset_password_step2.html.twig', [
                    'pass' => $passForm->createView(),
                ]);
            } else {
                $this->addFlash('error','Utilisateur inconnu');
                return $this->redirectToRoute('app_home');
            }
        } else {
            $this->addFlash('error','Lien expiré');
            return $this->redirectToRoute('app_home');
        } 
    }
}
