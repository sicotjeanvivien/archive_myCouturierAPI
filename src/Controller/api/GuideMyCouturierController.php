<?php

namespace App\Controller\api;

use App\Repository\GuideMyCouturierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/guide")
 */
class GuideMyCouturierController extends AbstractController
{
    private $guideMyCouturierRepository;
    private $serializerInterface;

    public function __construct(
        GuideMyCouturierRepository $guideMyCouturierRepository,
        SerializerInterface $serializerInterface
    ) {
        $this->guideMyCouturierRepository = $guideMyCouturierRepository;
        $this->serializerInterface = $serializerInterface;
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function show()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        $guide = $this->guideMyCouturierRepository->findAll();
        
        if (count($guide) > 0) {
            $guide = $this->serializerInterface->serialize($guide, 'json');
            $jsonContent = [
                'error' => false,
                'message' => '',
                'guide' => $guide
            ];
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
