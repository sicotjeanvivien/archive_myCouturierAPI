<?php

namespace App\Controller\api;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\PrestationsRepository;
use App\Repository\UserAppRepository;
use App\Service\MessageService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/message")
 */

class MessageController extends AbstractController
{
    private $em;
    private $prestationsRespository;
    private $userAppRepository;
    private $messageRepository;
    private $messageService;

    public function __construct(
        MessageService $messageService,
        EntityManagerInterface $entityManagerInterface,
        UserAppRepository $userAppRepository,
        PrestationsRepository $prestationsRespository,
        MessageRepository $messageRepository
    ) {
        $this->em = $entityManagerInterface;
        $this->userAppRepository = $userAppRepository;
        $this->prestationsRespository = $prestationsRespository;
        $this->messageRepository = $messageRepository;
        $this->messageService = $messageService;
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
            $message = new Message;
            $message = $this->messageService->set($message, $data, $request->headers->get('X-AUTH-TOKEN'));
            if ($message) {
                $this->em->persist($message);
                $this->em->flush();
                $jsonContent['error'] = false;
                $jsonContent['message'] = 'Message envoyé.';
            }
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
            $message = $this->messageRepository->findOneBy(['id' => $data['id']]);
            $message = $this->messageService->set($message, $data, $request->headers->get('X-AUTH-TOKEN'));

            if ($message) {
                $this->em->flush();
                $jsonContent['error'] = false;
                $jsonContent['message'] = 'Message modifié.';
            }
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
