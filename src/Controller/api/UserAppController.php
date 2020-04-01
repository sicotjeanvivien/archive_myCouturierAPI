<?php

namespace App\Controller\api;

use App\Entity\UserApp;
use App\Repository\UserAppRepository;
use App\Repository\UserPriceRetouchingRepository;
use App\Service\SecurityService;
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
    private $securityService;
    private $userPriceRetouchingRepository;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        SerializerInterface $serializerInterface,
        UserAppRepository $userAppRepository,
        UserAppService $userAppService,
        SecurityService $securityService,
        UserPriceRetouchingRepository $userPriceRetouchingRepository
    ) {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->serializerInterface = $serializerInterface;
        $this->userAppRepository = $userAppRepository;
        $this->userAppService = $userAppService;
        $this->securityService = $securityService;
        $this->userPriceRetouchingRepository = $userPriceRetouchingRepository;
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
                    ->setApitoken($this->securityService->tokenGenerator())
                    ->setPrivateMode(false)
                    ->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));

                $this->em->persist($user);
                $this->em->flush();
                $jsonContent['error'] = false;
                $jsonContent['message'] = 'compte créé';
                $jsonContent['user'] = $this->serializerInterface->serialize($user, 'json', ['groups' => 'group1']);
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
     * @Route("/api/account", methods="PATCH")
     */
    public function userApp_update(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {

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
     *  @Route("/api/password", methods="PATCH")
     */
    public function updatePassword(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {

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
     * @Route("/api/privateMode", methods="PATCH")
     */
    public function privateMode(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
            $userApp->setPrivateMode($data['privateMode']);
            $this->em->flush();

            $jsonContent['error'] = false;
            $jsonContent['message'] = $data['privateMode'] ? 'Vous étes en mode privé' : "vous n'étes plus en mode privé";
        }
        $response->setContent(json_encode($jsonContent));
        return  $response;
    }

    /**
     * @Route("/api/activeCouturier", methods="PATCH")
     */
    public function activeCouturier(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
            'activecouturier' => '',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
            $userApp->setActiveCouturier($data['activeCouturier']);
            $this->em->flush();

            $jsonContent['activeCouturier'] = $userApp->getActiveCouturier();
            $jsonContent['message'] = $userApp->getActiveCouturier() ? 'vous étes un couturier' : "vous n'étes plus couturier";
            $jsonContent['error'] = false;
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
        $userApp =  $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
        $userApp->setImageProfil(json_decode($request->getContent(), true));
        $this->em->flush();
        return $response;
    }

    /**
     *@Route("/api/searchPrestation", methods="POST") 
     */
    public function searchPrestation(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
            'couturier' => []
        ];

        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            // $retouche = $this->retouchingRepository->findBy(['type' => $data['search']]);
            $radius = !empty($data['radius']) ? $data['radius'] : 0.05;
            $longitude = !empty($data['longitude']) ? $data['longitude'] : 48.861017;
            $latitude = !empty($data['latitude']) ? $data['latitude'] : 2.3336696;
            $retouche = !empty($data['search']) ? $data['search'] : "noSelect";

            $couturierResultQuery = $this->userAppRepository->findCouturierBy($longitude, $latitude, $retouche, $radius);

            $dataCouturier = [];
            foreach ($couturierResultQuery as $user) {
                $priceClient = $this->userPriceRetouchingRepository->findPriceBy($user, $retouche);
                $dataCouturier[] = [
                    'id' => !empty($user->getId()) ? $user->getId() : null,
                    'username' => !empty($user->getUsername()) ? $user->getUsername() : 'ANONYMOUSLY',
                    'bio' => !empty($user->getBio()) ? $user->getBio() : '',
                    'imageProfil' => !empty($user->getImageProfil()) ? $user->getImageProfil() : null,
                    'longitude' => !empty($user->getLongitude()) ? $user->getLongitude() : 48.861017,
                    'latitude' => !empty($user->getLatitude()) ? $user->getLatitude() : 2.3336696,
                    'retouche' => [
                        'priceShowClient' => $priceClient['PriceShowClient'],
                        'type' => $retouche,
                    ]

                ];

            }
            $jsonContent['couturier'] = $dataCouturier;
            count($dataCouturier) > 0 ? $jsonContent['error'] = false && $jsonContent['message'] = '' : $jsonContent['error'] = true && $jsonContent['message'] = 'aucun couturier trouvé dans votre zone.';
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
