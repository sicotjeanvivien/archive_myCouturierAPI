<?php

namespace App\Controller;

use App\Repository\UserAppRepository;
use App\Service\MailerService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Serializer\SerializerInterface;


class SecurityController extends AbstractController
{

    private $em;
    private $securityService;
    private $serialazer;
    private $mailerService;
    private $userAppRepository;
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface $em,
        SecurityService $securityService,
        SerializerInterface $serialazer,
        MailerService $mailerService,
        UserAppRepository $userAppRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->em = $em;
        $this->securityService = $securityService;
        $this->serialazer = $serialazer;
        $this->mailerService = $mailerService;
        $this->userAppRepository = $userAppRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login_check", name="app_login_check", methods={"POST"})
     */
    public function loginCheck(Request $request): Response
    {
        $token = $this->securityService->tokenGenerator();

        $user = $this->getUser();
        $user->setApitoken($token);

        $jsonContent = $this->serialazer->serialize($user, 'json', ['groups' => 'group1']);
        $this->em->flush();

        $response = new Response();
        $response->setContent($jsonContent);
        return $response;
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        return $this->redirectToRoute('app_login');
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/passwordForgotten/{email}", methods={"GET"})
     */
    public function passwordForgotten(Request $request, $email)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];

        if ($request->headers->get('Content-Type', 'application/json') && !empty($userApp = $this->userAppRepository->findOneBy(['email' => $email]))) {

            $newPassword = $this->securityService->passwordGenerator();
            $userApp->setPassword($this->passwordEncoder->encodePassword($userApp, $newPassword));
            $this->em->flush();

            $to = $email;
            $subject = 'mot de passe oublié';
            $content = $this->renderView('emails/passwordForgotten.html.twig', [
                'newPassword' => $newPassword
            ]);
            $this->mailerService->sendEmail($to, $subject, $content);
            $jsonContent['message'] = 'email envoyé';
            $jsonContent['error'] = false;
        } else {
            $jsonContent['message'] = 'adresse email invalide';
        }
        $response->setContent(json_encode($jsonContent));
        return  $response;
    }
}
