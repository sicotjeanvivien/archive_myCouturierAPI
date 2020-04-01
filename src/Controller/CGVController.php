<?php

namespace App\Controller;

use App\Repository\ConfigAppRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CGVController extends AbstractController
{
    /**
     * @Route("/cgv", methods={"GET"})
     */
    public function showCGV(ConfigAppRepository $configAppRepository)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        $configApp = $configAppRepository->findOneBy(['site' => $_ENV['SITE']]);
        $cgv = $configApp->getCGV();
        if (!empty($cgv)) {
            $jsonContent['cgv'] = $cgv;
            $jsonContent['error']= false;
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
