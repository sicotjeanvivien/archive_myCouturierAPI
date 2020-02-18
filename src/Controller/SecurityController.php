<?php
namespace App\Controller;

use App\Service\MessageGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/login_check", name="app_login_check")
     */
    public function loginCheck(Request $request, MessageGenerator $messageGenerator): Response
    {        
        dump($request->headers, $request->getContent());

        $user = $this->getUser();

        $response = new Response();
        $response->setContent(json_encode([
            'username' => $user->getUsername(),
            'message' =>  $messageGenerator ->getHappyMessage()
        ]));
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
