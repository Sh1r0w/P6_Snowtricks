<?php

namespace App\Controller;

use App\Entity\Connect;
use App\Form\RegistrationFormType;
use App\Repository\ConnectRepository;
use App\Security\AppAuthenticator;
use App\Services\MailService;
use App\Services\JWTService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;


class RegistrationController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em, 
        private JWTService $jwt, 
        private AppAuthenticator $authenticator, 
        private UserAuthenticatorInterface $userAuthenticator, )
    {
        //$this->em = $em;
        //$this->jwt = $jwt;
        //$this->authenticator = $authenticator;
        //$this->userAuthenticator = $userAuthenticator;
    }
    
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager, 
        MailService $mail, 
        
        ): Response
    {
        $user = new Connect();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success','');

            // do anything else you need here, like send an email
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'user_id' => $user->getId()
            ];

            $token = $this->jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mail->send(
                'no-reply@snowtricks.com',
                $user->getEmail(),
                'Activation de votre compte',
                'register',
                compact('user', 'token')
            );
            
            // login after register
            return $this->userAuthenticator->authenticateUser(
                $user,
                $this->authenticator,
                $request
            );
            
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/{token}', name: 'verify_email')]
    public function verifyUserEmail(
        $token, 
        ConnectRepository $connectRepository, 
        Request $request, 
        ): Response
    {
        if($this->jwt->isValid($token) && !$this->jwt->isExpired($token) && $this->jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            $payload = $this->jwt->getPayload($token);
            $user = $connectRepository->find($payload['user_id']);

            
                if($user && !$user->isVerified()) {
                    $user->setIsVerified(true);
                    $this->em->flush($user);

                    return $this->userAuthenticator->authenticateUser(
                        $user,
                        $this->authenticator,
                        $request
                    );
            }
            
        }

        return $this->redirectToRoute('app_home');
    }
}
