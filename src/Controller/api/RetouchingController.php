<?php

namespace App\Controller\api;

use App\Repository\RetouchingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *@Route("/api/retouching") 
 */
class RetouchingController extends AbstractController
{

    private $retouchingRepository;
    private $serializer;
    private $serializerInterface;

    public function __construct(
        RetouchingRepository $retouchingRepository
    ) {
        $this->retouchingRepository = $retouchingRepository;
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function show()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [];

        $json = $this->retouchingRepository->findAllRetouche();

        dump($json);


        $jsonContent = [
            'error'=>false,
            'retouches' => $json
        ];

        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
