<?php

namespace App\Controller\api;

use App\Entity\Prestations;
use App\Repository\PrestationsRepository;
use App\Repository\RetouchingRepository;
use App\Repository\UserAppRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;



/**
 *@Route("/api") 
 */
class PrestationController
{
    public $userAppRepository;
    public $prestationsRepository;

    public function __construct(UserAppRepository $userAppRepository, PrestationsRepository $prestationsRepository)
    {
        $this->userAppRepository = $userAppRepository;
        $this->prestationsRepository = $prestationsRepository;
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
}
