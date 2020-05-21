<?php

namespace App\Controller\api;

use App\Entity\Commentary;
use App\Repository\CommentaryRepository;
use App\Repository\PrestationsRepository;
use App\Repository\UserAppRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/Commentary")
 */

class CommentaryController extends AbstractController
{

    private $em;
    private $commentaryRepository;
    private $userAppRepository;
    private $prestationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PrestationsRepository $prestationRepository,
        CommentaryRepository $commentaryRepository,
        UserAppRepository $userAppRepository
    ) {
        $this->em = $entityManager;
        $this->prestationRepository = $prestationRepository;
        $this->userAppRepository = $userAppRepository;
        $this->commentaryRepository = $commentaryRepository;
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

            $prestation = $this->prestationRepository->findOneBy(['id' => $data['prestationId']]);

            $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
            $couturier = $prestation->getUserPriceRetouching()->getUserApp();
            $commentary = new Commentary();
            $commentary
                ->setRating($data['rating'])
                ->setCouturier($couturier)
                ->setMessage($data['message'])
                ->setAuthor($userApp);
            $this->em->persist($commentary);
            $this->em->flush();
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
