<?php

namespace App\Controller;

use App\Entity\UserApp;
use App\Repository\UserAppRepository;
use App\Service\UserAppService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UserAppController
{

    private $em;
    private $passwordEncoder;
    private $serializerInterface;
    private $userAppRepository;
    private $userAppService;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        SerializerInterface $serializerInterface,
        UserAppRepository $userAppRepository,
        UserAppService $userAppService
    ) {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->serializerInterface = $serializerInterface;
        $this->userAppRepository = $userAppRepository;
        $this->userAppService = $userAppService;
    }

    /**
     * TODOO
     * @Route("/userapp_create", methods={"POST"})
     */
    public function userApp_create(Request $request)
    {
        $response = new Response();

        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type', 'application/json')) {

            if (
                !empty($data['password'])
                && !empty($data['passwordConfirm'])
                && strlen($data['password']) > 7
                && $data['password'] === $data['passwordConfirm']
            ) {
                $user = $this->serializer->deserialize($request->getContent(), UserApp::class, 'json');
                $user->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    $data['password']
                ));
                $this->em->persist($user);
                $this->em->flush();
            }
            //TODOO
            return $response;
        } else {
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent('Error');
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
            'error'=> true,
            'message'=> 'error server',
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
            'error'=> true,
            'message'=> 'error server',
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
}
