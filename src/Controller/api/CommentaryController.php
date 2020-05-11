<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/Commentary")
 */

class CommentaryController extends AbstractController
{
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
        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/", methods={"POST"})
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
           
            //todoo
            
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
        
    }

    /**
     * @Route("/", methods={"PUT"})
     */
    public function update(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
          
            //TODOO
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
        
    }

    /**
     * @Route("/", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            
            ///TODOO
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
        
    }

}