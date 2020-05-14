<?php

namespace App\Controller;

use App\Repository\UserAppRepository;
use App\Service\MailerService;
use App\Service\SecurityService;
use App\Service\UserAppService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\UserApp;
use App\Service\MangoPayService;
use Symfony\Component\Validator\Constraints\Date;

class SecurityController extends AbstractController
{

    private $em;
    private $securityService;
    private $serializerInterface;
    private $mailerService;
    private $userAppRepository;
    private $passwordEncoder;
    private $mangoPayService;
    private $userappService;

    public function __construct(
        EntityManagerInterface $em,
        SecurityService $securityService,
        SerializerInterface $serializerInterface,
        MailerService $mailerService,
        UserAppRepository $userAppRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        UserAppService $userAppService,
        MangoPayService $mangoPayService
    ) {
        $this->em = $em;
        $this->securityService = $securityService;
        $this->mangoPayService = $mangoPayService;
        $this->serializerInterface = $serializerInterface;
        $this->mailerService = $mailerService;
        $this->userAppRepository = $userAppRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->userAppService = $userAppService;
    }

    /**
     * @Route("/login_check", name="app_login_check", methods={"POST"})
     */
    public function loginCheck(Request $request): Response
    {
        $token = $this->securityService->tokenGenerator();

        $user = $this->getUser();
        $user->setApitoken($token);
        $jsonContent = [
            'activeCouturier' => $user->getActiveCouturier() ? 'true' : 'false',
            'apitoken' => $user->getApitoken(),
            'bio' => $user->getBio(),
            'email' => $user->getEmail(),
            'fisrtname' => $user->getFirstname(),
            'id' => strval($user->getId()),
            'imageProfil' => $user->getImageProfil(),
            'lastname' => $user->getLastname(),
            'privateMode' => $user->getPrivateMode() ? 'true' : 'false',
            'username' => $user->getUsername(),
        ];
        $this->em->flush();
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/userapp_create", methods={"POST"})
     */
    public function userApp_create(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {

            $valideDataAccount = $this->userAppService->validateDataAccount($data);
            $validePassword = $this->userAppService->validateDataPassword($data);

            if (!$valideDataAccount['error'] && !$validePassword['error']) {
                $user = $this->serializerInterface->deserialize($request->getContent(), UserApp::class, 'json');
                $mangoUser = $this->mangoPayService->setMangoUser($data);
                if (isset($mangoUser->Errors)) {
                    $jsonContent = [
                        'error' => true,
                        'message' => $mangoUser,
                    ];
                    $response->setContent(json_encode($jsonContent));
                    return $response;
                }
                $mangoWallet = $this->mangoPayService->setMangoWallet($mangoUser->Id);
                if (isset($mangoWallet->Errors)) {
                    $jsonContent = [
                        'error' => true,
                        'message' => $mangoWallet,
                    ];
                    $response->setContent(json_encode($jsonContent));
                    return $response;
                }
                $user
                    ->setUsername($user->getFirstname() . ' ' . $user->getLastname()[0])
                    ->setRoles(['ROLE_USER'])
                    ->setApitoken($this->securityService->tokenGenerator())
                    ->setPrivateMode(false)
                    ->setCreationDate(new \DateTime('NOW'))
                    ->setAddress($data['address'])
                    ->setMangoUserId($mangoUser->Id)
                    ->setMangoWalletId($mangoWallet->Id)
                    ->setLongitude(isset($data['longitude']) ? floatval($data['longitude']) : 0)
                    ->setLatitude(isset($data['latitude']) ? floatval($data['latitude']) : 0)
                    ->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));

                $this->em->persist($user);
                $this->em->flush();
                $jsonContent['error'] = false;
                $jsonContent['message'] = 'compte créé';
                $jsonContent['user'] = $this->serializerInterface->serialize($user, 'json', ['groups' => 'group1']);

                $content =  $this->renderView('emails/createAccount.html.twig');
                $this->mailerService->sendEmail($user->getEmail(), 'create account', $content);
            } else {
                $jsonContent['message'] = $valideDataAccount['message'] . $validePassword['message'];
            }
            $response->setContent(json_encode($jsonContent));
            return $response;
        } else {
            $response->setContent(json_encode($jsonContent));
            return  $response;
        }
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
