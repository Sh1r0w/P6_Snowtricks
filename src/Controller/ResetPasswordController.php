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
    /**
     * The function is a constructor that initializes several dependencies for a PHP class.
     */
    public function __construct(
        private ManagerRegistry $doctrine,
        private JWTService $jwt,
        private MailService $mail,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $userPasswordHasher,

    ) {
    }

    /**
     * This PHP function handles the submission of a password reset form, generates a JWT token,
     * updates the user's reset token in the database, sends a password reset email, and displays a
     * success or error message.
     * 
     * @param Request request The `` parameter is an instance of the `Request` class, which
     * represents an HTTP request. It contains information about the request, such as the request
     * method, headers, query parameters, and request body.
     * 
     * @return Response The code is returning a Response object.
     */
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

    /**
     * The function `resetPassword2` is used to reset a user's password by validating a token, updating
     * the password, and displaying a form for the user to enter their new password.
     * 
     * @param token The `token` parameter is a string that represents a unique token generated for a
     * user who wants to reset their password. This token is typically sent to the user's email address
     * and is used to verify the user's identity and authorize the password reset process.
     * @param request request The `` parameter is an instance of the
     * `Symfony\Component\HttpFoundation\Request` class. It represents the current HTTP request and
     * contains information such as the request method, headers, query parameters, and request body. It
     * is used to handle and process the form submission in this code snippet.
     * 
     * @return Response a Response object.
     */
    #[Route('reset/{token}', methods:['GEt', 'POST'] ,name: 'app_reset_step_2')]
    public function resetPassword2(
        string $token,
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
