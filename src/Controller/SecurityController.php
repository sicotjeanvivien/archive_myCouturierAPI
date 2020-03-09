<?php

namespace App\Controller;

use App\Service\MessageGenerator;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Serializer\SerializerInterface;


class SecurityController extends AbstractController
{

    private $em;
    private $tokenService;
    private $serialazer;

    public function __construct(EntityManagerInterface $em, TokenService $tokenService, SerializerInterface $serialazer)
    {
        $this->em = $em;
        $this->tokenService = $tokenService;
        $this->serialazer = $serialazer;
    }

    /**
     * @Route("/login_check", name="app_login_check")
     */
    public function loginCheck(Request $request): Response
    {

//TODOO response secu et secu et encore secu

        dump($request);
        $token = $this->tokenService->tokenGenerator();

        $user = $this->getUser();
        $user->setApitoken($token);

        $jsonContent = $this->serialazer->serialize($user,'json', ['groups' => 'group1']); 
        $this->em->flush();

        dump($jsonContent);

        $response = new Response();
        $response->setContent($jsonContent);
        return $response;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        return $this->redirectToRoute('app_login');
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
