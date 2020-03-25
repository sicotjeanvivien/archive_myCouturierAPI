<?php

namespace App\Controller;

use App\Repository\ConfigAppRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ConfigAppController extends AbstractController
{
    /**
     * @Route("/cgvShow", methods={"GET"})
     */
    public function cgvShow(Request $request, ConfigAppRepository $configAppRepository)
    {
        $response = new Response();
        $cgv = $configAppRepository->findOneBy(['site'=>'MyCouturier']);
        $response->setContent(json_encode( $cgv->getCGV()));
        return $response;
        
    }

}