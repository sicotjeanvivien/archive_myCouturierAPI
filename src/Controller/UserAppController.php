<?php

namespace App\Controller;

use App\Entity\UserApp;
use App\Repository\UserAppRepository;
use App\Service\TokenService;
use App\Service\UserAppService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserAppController
{

    private $em;
    private $passwordEncoder;
    private $serializerInterface;
    private $userAppRepository;
    private $userAppService;
    private $tokenService;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        SerializerInterface $serializerInterface,
        UserAppRepository $userAppRepository,
        UserAppService $userAppService,
        TokenService $tokenService
    ) {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->serializerInterface = $serializerInterface;
        $this->userAppRepository = $userAppRepository;
        $this->userAppService = $userAppService;
        $this->tokenService = $tokenService;
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
        
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type', 'application/json')) {


            $valideDataAccount = $this->userAppService->validateDataAccount($data);
            $validePassword = $this->userAppService->validateDataPassword($data);

            if (!$valideDataAccount['error'] && !$validePassword['error']) {
                $user = $this->serializerInterface->deserialize($request->getContent(), UserApp::class, 'json');
                $user
                    ->setRoles(['ROLE_USER'])
                    ->setApitoken($this->tokenService->tokenGenerator())
                    ->setPrivateMode(false)
                    ->setPassword($this->passwordEncoder->encodePassword($user, $data['password']))
                ;

                $this->em->persist($user);
                $this->em->flush();
                $jsonContent['error'] = false;
                $jsonContent['message'] = 'compte créé';
                $jsonContent['user'] = $this->serializerInterface->serialize($user,'json', ['groups' => 'group1']);
            }else {
                $jsonContent['message'] = $valideDataAccount['message'].$validePassword['message'];
            }
            $response->setContent(json_encode($jsonContent));
            return $response;
        } else {
            $response->setContent(json_encode($jsonContent));
            return  $response;
        }
    }

    /**
     * @Route("/api/account", methods="PATH")
     */
    public function userApp_update(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type', 'application/json')) {

            $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
            $dataValide = $this->userAppService->validateDataAccount($data);

            if (!$dataValide['error']) {
                $userApp
                    ->setUsername($data['username'])
                    ->setFirstname($data['firstname'])
                    ->setLastname($data['lastname'])
                    ->setEmail($data['email']);
                $this->em->flush($userApp);
                $jsonContent['error'] = false;
                $jsonContent['message'] = 'Information du compte mise à jour.';
            } else {
                $jsonContent['message'] = $dataValide['message'];
            }
            $response->setContent(json_encode($jsonContent));
            return  $response;
        } else {
            $response->setContent(json_encode($jsonContent));
            return  $response;
        }
    }

    /**
     *  @Route("/api/password", methods="PATH")
     */
    public function updatePassword(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type', 'application/json')) {

            $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
            $dataValide = $this->userAppService->validateDataPassword($data);

            if (!$dataValide['error']) {
                $userApp->setPassword($this->passwordEncoder->encodePassword(
                    $userApp,
                    $data['password']
                ));
                $this->em->flush($userApp);
                $jsonContent['error'] = false;
                $jsonContent['message'] = 'Mot de passse mise à jour.';
            } else {
                $jsonContent['message'] = $dataValide['message'];
            }
            $response->setContent(json_encode($jsonContent));
            return  $response;
        } else {
            $response->setContent(json_encode($jsonContent));
            return  $response;
        }
    }

    /**
     * @Route("/api/privateMode", methods="PATH")
     */
    public function privateMode(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type', 'application/json')) {
            $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
            $userApp->setPrivateMode($data['privateMode']);
            $this->em->flush();

            $jsonContent['error'] = false;
            $jsonContent['message'] = $data['privateMode']?'Vous étes en mode privé':"vous n'étes plus en mode privé";
        }
        $response->setContent(json_encode($jsonContent));
        return  $response;
    }

    /**
     * @Route("/api/imageProfil", methods="POST")
     */
    public function uploadImageProfil(Request $request)
    {
        $response = new Response();
        $userApp =  $this->userAppRepository->findOneBy(['apitoken'=> $request->headers->get('X-AUTH-TOKEN')]);
        $userApp->setImageProfil(json_decode($request->getContent(), true));
        $this->em->flush();
        return $response;
    }
}
