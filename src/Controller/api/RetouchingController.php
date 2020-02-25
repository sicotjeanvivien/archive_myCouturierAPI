<?php

namespace App\Controller\api;

use App\Repository\RetouchingRepository;
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
    private $retouchingRepositoty;

    public function __construct(EntityManagerInterface $em, RetouchingRepository $retouchingRepository)
    {
        $this->em = $em;
        $this->retouchingRepository = $retouchingRepository;
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
        $jsonContent = $this->serializer->serialize($retouching, 'json');

        dump($jsonContent);

        $response = new Response;
        $response
            ->setContent($jsonContent)
            ->headers->set('Content-Type', 'application/json');
    
        dump('hell654464o');

        return $response;
    }
   
   
    /**
     * @Route("/retouching", methods={"POST"})
     */
    public function retouchingCreate(Request $request)
    {
        // dump($request->headers);
        $response = new Response;
        $response->getContent('hello');
        dump('hell654464o');

        return $response;
    }


}
