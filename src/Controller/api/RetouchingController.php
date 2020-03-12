<?php

namespace App\Controller\api;

use App\Entity\UserPriceRetouching;
use App\Repository\RetouchingRepository;
use App\Repository\UserAppRepository;
use App\Repository\UserPriceRetouchingRepository;
use App\Service\PrestationsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 *@Route("/api") 
 */
class RetouchingController
{

    private $em;
    private $retouchingRepository;
    private $userAppRepository;
    private $userPriceRetouchingRepository;
    private $prestationsService;

    public function __construct(
        EntityManagerInterface $em,
        RetouchingRepository $retouchingRepository,
        UserAppRepository $userAppRepository,
        UserPriceRetouchingRepository $userPriceRetouchingRepository,
        PrestationsService $prestationsService
    ) {
        $this->em = $em;
        $this->retouchingRepository = $retouchingRepository;
        $this->userAppRepository = $userAppRepository;
        $this->userPriceRetouchingRepository = $userPriceRetouchingRepository;
        $this->prestationsService = $prestationsService;
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getType();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $this->serializer = new Serializer([$normalizer], $encoders);
    }


    /**
     * @Route("/retouching", methods={"GET"})
     */
    public function retouchingShow(Request $request)
    {

        $retouching = $this->retouchingRepository->findAll();
        $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
        $jsonContent= [];
        foreach ($retouching as $retouche) {
            $priceCouturier = $this->userPriceRetouchingRepository->findOnePrice($retouche, $userApp);
            $jsonContent[] = [
                'id' => !empty($retouche->getId()) ? $retouche->getId() : '',
                'CategoryRetouching' => !empty($retouche->getCategoryRetouching()->getType()) ? $retouche->getCategoryRetouching()->getType() : '',
                'type' => !empty($retouche->getType()) ? $retouche->getType() : '',
                'description' => !empty($retouche->getDescription()) ? $retouche->getDescription() : '',
                'value' => !empty($priceCouturier) ? strval($priceCouturier['PriceCouturier']) : '',
            ];
        }
        // $jsonContent = $this->serializer->serialize($retouching, 'json');
        dump($jsonContent);

        $response = new Response;
        $response
            ->setContent(json_encode($jsonContent))
            ->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/retouching", methods={"PUT"})
     */
    public function retouchingCreate(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type', 'application/json')) {
            $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
            $userApp
                ->setActiveCouturier($data['activeCouturier'])
                ->setBio($data['bio']);
            $jsonContent['error'] = false;
            $jsonContent['message'] = 'information de profil mis à jour';

            if ($data['activeCouturier'] === $userApp->getActiveCouturier()) {
                foreach ($data['retouche'] as $retouche) {
                    if ($retouche['active']) {
                        $retouching = $this->retouchingRepository->findOneBy(['id' => $retouche['id']]);
                        $countUserPriceRetouching = $this->userPriceRetouchingRepository->countUserPriceRetouching($userApp, $retouching);
                        $userPriceRetouching =  $this->userPriceRetouchingRepository->findOneBy(['UserApp' => $userApp, 'Retouching' => $retouching]);
                        $priceClient = $this->prestationsService->calculPriceClient(intval($retouche['value']));
                        if (intval($countUserPriceRetouching) === 1) {
                            $userPriceRetouching
                                ->setPriceCouturier(intval($retouche['value']))
                                ->setPriceShowClient(intval($priceClient));
                        } else if (intval($countUserPriceRetouching) < 1) {
                            $newUserPriceRetouching = new UserPriceRetouching();
                            $newUserPriceRetouching
                                ->setRetouching($retouching)
                                ->setUserApp($userApp)
                                ->setPriceCouturier(intval($retouche['value']))
                                ->setPriceShowClient(intval($priceClient));
                            $this->em->persist($newUserPriceRetouching);
                        }
                    }
                }
            }
            $this->em->flush();
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
