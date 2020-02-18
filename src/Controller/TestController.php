<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test", methods={"GET"})
     */
    public function login(Request $request)
    {
        dump($request);
        $response = new Response();
        $response->setContent(json_encode( 'hello'));
        return $response;
        
    }

}