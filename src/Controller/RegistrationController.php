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

   /**
    * The function is a constructor that takes in four dependencies: EntityManagerInterface,
    * JWTService, AppAuthenticator, and UserAuthenticatorInterface.
    */
    public function __construct(
        private EntityManagerInterface $em, 
        private JWTService $jwt, 
        private AppAuthenticator $authenticator, 
        private UserAuthenticatorInterface $userAuthenticator, )
    {
    }
    
   /**
    * This PHP function handles user registration, including password hashing, persisting the user to
    * the database, sending an activation email, and authenticating the user after registration.
    * 
    * @param Request request The `` parameter is an instance of the `Request` class, which
    * represents an HTTP request. It contains information about the request, such as the request
    * method, headers, and request data.
    * @param UserPasswordHasherInterface userPasswordHasher The `UserPasswordHasherInterface` is used
    * to hash the user's password before storing it in the database. It provides a method
    * `hashPassword()` which takes the user object and the plain password as arguments and returns the
    * hashed password.
    * @param EntityManagerInterface entityManager The `entityManager` parameter is an instance of the
    * `EntityManagerInterface` class, which is responsible for managing the persistence of objects to
    * the database. It provides methods for persisting, updating, and deleting entities, as well as
    * querying the database.
    * @param MailService mail The `` parameter is an instance of the `MailService` class, which is
    * responsible for sending emails. It is used to send an activation email to the user after they
    * register.
    * 
    * @return Response a Response object.
    */
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

    /**
     * The function verifies a user's email by checking the validity and expiration of a token,
     * updating the user's verification status, and authenticating the user if successful.
     * 
     * @param token The `token` parameter is a string that represents the verification token for the
     * user's email. This token is typically generated and sent to the user's email address when they
     * sign up or request to verify their email.
     * @param ConnectRepository connectRepository The `connectRepository` is an instance of a
     * repository class that is responsible for retrieving and manipulating data related to the
     * `Connect` entity. It is used to find a user based on the `user_id` extracted from the JWT token
     * payload.
     * @param Request request The `` parameter is an instance of the
     * `Symfony\Component\HttpFoundation\Request` class. It represents an HTTP request made to the
     * server and contains information such as the request method, headers, query parameters, and
     * request body.
     * 
     * @return Response If the conditions in the if statement are met, the function will return the
     * result of the `authenticateUser` method. Otherwise, it will return the result of the
     * `redirectToRoute` method with the argument `'app_home'`.
     */
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
