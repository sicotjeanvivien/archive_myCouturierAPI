<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\UserApp;
use App\Repository\UserAppRepository;
use App\Repository\UserPriceRetouchingRepository;
use App\Service\MailerService;
use App\Service\SecurityService;
use App\Service\UserAppService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/userapp")
 */
class UserAppController extends AbstractController
{
    private $em;
    private $passwordEncoder;
    private $serializerInterface;
    private $userAppRepository;
    private $userAppService;
    private $securityService;
    private $userPriceRetouchingRepository;
    private $mailerService;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        SerializerInterface $serializerInterface,
        UserAppRepository $userAppRepository,
        UserAppService $userAppService,
        SecurityService $securityService,
        UserPriceRetouchingRepository $userPriceRetouchingRepository,
        MailerService $mailerService
    ) {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->serializerInterface = $serializerInterface;
        $this->userAppRepository = $userAppRepository;
        $this->userAppService = $userAppService;
        $this->securityService = $securityService;
        $this->userPriceRetouchingRepository = $userPriceRetouchingRepository;
        $this->mailerService = $mailerService;
    }

    /**
     * @Route("/update", methods="PATCH")
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
                    ->setFirstname(empty($data['firstname']) ? $userApp->getFirstname() : $data['firstname'])
                    ->setLastname(empty($data['lastname']) ? $userApp->getLastname() :  $data['lastname'])
                    ->setBio(empty($data['bio'] ? $userApp->getBio() : $data['bio']))
                    ->setEmail(empty($data['email']) ? $userApp->getEmail() : $data['email']);
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
     *  @Route("/password", methods="PATCH")
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
     * @Route("/privateMode", methods="PATCH")
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
     * @Route("/activeCouturier", methods="PATCH")
     */
    public function activeCouturier(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
            $userApp->setActiveCouturier($data['activeCouturier']);
            $this->em->flush();

            $jsonContent['activeCouturier'] = $userApp->getActiveCouturier() ? 'true' : 'false';
            $jsonContent['message'] = $userApp->getActiveCouturier() ? 'vous étes un couturier' : "vous n'étes plus couturier";
            $jsonContent['error'] = false;
        }

        $response->setContent(json_encode($jsonContent));
        return  $response;
    }


    /**
     * @Route("/imageProfil", methods="POST")
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
     *@Route("/searchPrestation", methods="POST") 
     */
    public function searchPrestation(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $couturierResultQuery = [];
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
            $retouche = !empty($data['search']) ? $data['search'] : 'noSelect';
            $dataCouturier = [];

            if ($retouche === 'noSelect') {
                $couturierResultQuery = $this->userAppRepository->findAllCouturierBy($longitude, $latitude, $radius);
                foreach ($couturierResultQuery as $user) {
                    $detailRetouche = $this->userPriceRetouchingRepository->findPriceBy($user, $retouche);
                    $dataCouturier[] = [
                        'id' => !empty($user->getId()) ? $user->getId() : null,
                        'username' => !empty($user->getUsername()) ? $user->getUsername() : 'ANONYMOUSLY',
                        'bio' => !empty($user->getBio()) ? $user->getBio() : '',
                        'raiting' => !empty($user->getRaitingCouturier) ? $user->getRaitingCouturier : '',
                        'imageProfil' => !empty($user->getImageProfil()) ? $user->getImageProfil() : null,
                        'longitude' => !empty($user->getLongitude()) ? $user->getLongitude() : 48.861017,
                        'latitude' => !empty($user->getLatitude()) ? $user->getLatitude() : 2.3336696,
                    ];
                }
            } else {
                $couturierResultQuery = $this->userAppRepository->findCouturierBy($longitude, $latitude, $retouche, $radius);
                foreach ($couturierResultQuery as $user) {
                    $detailRetouche = $this->userPriceRetouchingRepository->findPriceBy($user, $retouche);
                    $dataCouturier[] = [
                        'id' => !empty($user->getId()) ? $user->getId() : null,
                        'username' => !empty($user->getUsername()) ? $user->getUsername() : 'ANONYMOUSLY',
                        'bio' => !empty($user->getBio()) ? $user->getBio() : '',
                        'raiting' => !empty($user->getRaitingCouturier) ? $user->getRaitingCouturier : '',
                        'imageProfil' => !empty($user->getImageProfil()) ? $user->getImageProfil() : null,
                        'longitude' => !empty($user->getLongitude()) ? $user->getLongitude() : 48.861017,
                        'latitude' => !empty($user->getLatitude()) ? $user->getLatitude() : 2.3336696,
                        'retouche' => [
                            // 'test'=> $detailRetouche->getPriceShowClient() ,
                            'id' => !empty($detailRetouche->getId()) ? $detailRetouche->getId() : '',
                            'priceShowClient' => !empty($detailRetouche->getPriceShowClient()) ? $detailRetouche->getPriceShowClient() : '',
                            'tool' => !empty($detailRetouche->getTool()) ? $detailRetouche->getTool() : '',
                            'deadline' => !empty($detailRetouche->getDeadline()) ? $detailRetouche->getDeadline() : '',
                            'commitment' => !empty($detailRetouche->getCommitment()) ? $detailRetouche->getCommitment() : '',
                            'deadline' => !empty($detailRetouche->getDeadline()) ? $detailRetouche->getDeadline() : '',
                            'type' => $retouche,
                        ]

                    ];
                }
            }
            $jsonContent['couturier'] = $dataCouturier;
            count($dataCouturier) > 0 ? $jsonContent['error'] = false && $jsonContent['message'] = '' : $jsonContent['error'] = true && $jsonContent['message'] = 'aucun couturier trouvé dans votre zone.';
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/delete", methods="POST")
     */
    public function delateAccount(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];

        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $userApp = $this->userAppRepository->findOneBy(['email' => $data['email']]);
            $this->em->remove($userApp);
            $this->em->flush();
            $jsonContent['error'] = false;
        }

        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
