<?php

namespace App\Controller\api;

use App\Entity\Prestations;
use App\Repository\PrestationsRepository;
use App\Repository\RetouchingRepository;
use App\Repository\UserAppRepository;
use App\Repository\UserPriceRetouchingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;



/**
 *@Route("/api") 
 */
class PrestationController
{
    private $userAppRepository;
    private $prestationsRepository;
    private $retouchingRepository;
    private $userPriceRetouchingRepository;
    private $em;

    public function __construct(
        UserAppRepository $userAppRepository,
        PrestationsRepository $prestationsRepository,
        RetouchingRepository $retouchingRepository,
        UserPriceRetouchingRepository $userPriceRetouchingRepository,
        EntityManagerInterface $entityManagerInterface
    ) {
        $this->em = $entityManagerInterface;
        $this->userAppRepository = $userAppRepository;
        $this->prestationsRepository = $prestationsRepository;
        $this->retouchingRepository = $retouchingRepository;
        $this->userPriceRetouchingRepository = $userPriceRetouchingRepository;
    }

    /**
     * @Route("/prestations", methods="GET")
     */
    public function prestationsShow(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
            'prestationsINPROGRESS' => [],
            'prestationsEND' => [],
        ];
        $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
        $prestationsINPROGRESS = $this->prestationsRepository->findlastStatutByUserApp($userApp->getId(), Prestations::ACTIVE);
        $prestationsEND = $this->prestationsRepository->findlastStatutByUserApp($userApp->getId(), Prestations::INACTIVE);
        $jsonContent['prestationsINPROGRESS'] = $prestationsINPROGRESS;
        $jsonContent['prestationsEND'] = $prestationsEND;

        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/prestationDetail/{id}", methods="GET")
     */
    public function prestationDetail($id)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];

        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/api/createPrestation", methods="POST")
     */
    public function create(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];

        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $userPriceRetouching = $this->userPriceRetouchingRepository->findOneBy(['id' => $data['retoucheId']]);
            $client =  $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);

            $prestation = new Prestations();
            $prestation
                ->setClient($client)
                ->setDescription($data['description'])
                ->setPhoto($data['photo'])
                ->setState(Prestations::ACTIVE)
                ->setUserPriceRetouching($userPriceRetouching);

            $this->em->persist($prestation);
            $this->em->flush();
        }

        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/api/prestationAcccept", methods={"PATCH"})
     */
    public function acceptPrestation(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];

        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $prestation = $this->prestationsRepository->findOneBy(['id' => $data['id']]);
            $prestation->setAccept($data['accept'] ? Prestations::ACTIVE : Prestations::INACTIVE);
            $this->em->flush();
            $jsonContent['error'] = false;
            $jsonContent['message'] = $data['accept'] ? 'Prestation acceptée.' : 'Prestation déclinée.';
        }

        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
