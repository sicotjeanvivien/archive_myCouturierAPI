<?php

namespace App\Controller\api;

use App\Entity\PrestationHistory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Prestations;
use App\Entity\StatutHistory;
use App\Repository\MessageRepository;
use App\Repository\PrestationHistoryRepository;
use App\Repository\PrestationsRepository;
use App\Repository\RetouchingRepository;
use App\Repository\StatutHistoryRepository;
use App\Repository\UserAppRepository;
use App\Repository\UserPriceRetouchingRepository;
use App\Service\MangoPayService;
use App\Service\PrestationsService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 *@Route("/api/prestation") 
 */
class PrestationController extends AbstractController
{
    private $userAppRepository;
    private $prestationsRepository;
    private $prestationHistoryRepository;
    private $userPriceRetouchingRepository;
    private $prestationService;
    private $statutHistoryRepository;
    private $messageRepository;
    private $em;

    public function __construct(
        StatutHistoryRepository $statutHistoryRepository,
        UserAppRepository $userAppRepository,
        PrestationsRepository $prestationsRepository,
        RetouchingRepository $retouchingRepository,
        UserPriceRetouchingRepository $userPriceRetouchingRepository,
        EntityManagerInterface $entityManagerInterface,
        MangoPayService $mangoPayService,
        PrestationsService $prestationService,
        PrestationHistoryRepository $prestationHistoryRepository,
        MessageRepository $messageRepository
    ) {
        $this->em = $entityManagerInterface;
        $this->mangoPayService = $mangoPayService;
        $this->statutHistoryRepository = $statutHistoryRepository;
        $this->prestationService = $prestationService;
        $this->userAppRepository = $userAppRepository;
        $this->prestationsRepository = $prestationsRepository;
        $this->retouchingRepository = $retouchingRepository;
        $this->userPriceRetouchingRepository = $userPriceRetouchingRepository;
        $this->prestationHistoryRepository = $prestationHistoryRepository;
        $this->messageRepository = $messageRepository;
    }

    /**
     * @Route("/", methods="GET")
     */
    public function prestationsShow(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
        if ($userApp) {
            $jsonContent['client'] = $this->prestationService->prestaClient($userApp);
            $jsonContent['couturier'] = $this->prestationService->prestaCouturier($userApp);
            $jsonContent['error'] = false;
            $jsonContent['message'] = "it's ok";
        }
        $jsonContent['id'] = $userApp->getId();

        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/{id}", methods="GET")
     */
    public function prestationDetailClient(Request $request, $id)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];

        $prestation = $this->prestationsRepository->findOneBy(['id' => $id]);
        $prestationHistory = $this->prestationHistoryRepository->findAllByPrestation($prestation);
        $message = $this->messageRepository->findAllByPrestation($prestation);

        dump(!empty($prestation), !empty($prestationHistory), !empty($message));

        if ($request->headers->get('Content-Type') === 'application/json' && !empty($prestation)) {

            $jsonContent['prestation'] = [
                'id' => $prestation->getId(),
                'client' => $prestation->getClient()->getUsername(),
                'couturier' => $prestation->getUserPriceRetouching()->getUserApp()->getUsername(),
                'state' => $prestation->getState() ? $prestation->getState() : null,
                'description' => $prestation->getDescription() ? $prestation->getDescription() : null,
                'photo' => $prestation->getPhoto() ? $prestation->getPhoto() : null,
                'accept' => $prestation->getAccept() ? $prestation->getAccept() : null,
                'pay' => $prestation->getPay() ? $prestation->getPay() : null,
                'priceShow' => $prestation->getUserPriceRetouching()->getPriceShowClient(),
                'priceCouturier' => $prestation->getUserPriceRetouching()->getPriceCouturier(),
                'deadline' => $prestation->getUserPriceRetouching()->getDeadline(),
                'tool' => $prestation->getUserPriceRetouching()->getTool(),
                'commitment' => $prestation->getUserPriceRetouching()->getCommitment(),
                'history' => $prestationHistory,
                'message' => $message,

            ];
            $jsonContent['error'] = false;
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/", methods="POST")
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
            $userPriceRetouching = $this->userPriceRetouchingRepository->findOneBy(['id' => $data['retoucheId']]);
            $client =  $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
            if (!empty($userPriceRetouching) && !empty($client)) {
                $prestation = new Prestations();
                $prestation
                    ->setClient($client)
                    ->setDescription(!empty($data['description']) ? $data['description'] : null)
                    ->setPhoto(!empty($data['photo']) ? $data['photo'] : null)
                    ->setState(Prestations::ACTIVE)
                    ->setUserPriceRetouching($userPriceRetouching);
                $this->em->persist($prestation);
                $prestationHistory = new PrestationHistory();
                $prestationHistory
                    ->setDate(new DateTime('now'))
                    ->setStatut($this->statutHistoryRepository->findOneBy(['statut' => StatutHistory::DEMANDE]))
                    ->setPrestation($prestation);
                $this->em->persist($prestationHistory);
                $this->em->flush();
                $jsonContent['error'] = false;
                $jsonContent['message'] = 'Votre demande a été envoyée au couturier.';
            } else {
                $jsonContent['message'] = 'error data';
            }
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/accept", methods={"PATCH"})
     */
    public function acceptPrestation(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];

        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $prestation = $this->prestationsRepository->findOneBy(['id' => $data['id']]);
            $prestation->setAccept($data['accept'] ? true : false);
            $prestation->setState($data['accept'] ? Prestations::ACTIVE : Prestations::INACTIVE);
            $statut = $this->statutHistoryRepository->findOneBy(['statut' => StatutHistory::ACCEPT]);
            $prestationHistory = new PrestationHistory();
            $prestationHistory
                ->setDate(new DateTime('now'))
                ->setStatut(isset($statut) ? $statut : null)
                ->setPrestation($prestation);
            $this->em->persist($prestationHistory);
            $this->em->flush();
            $jsonContent['error'] = false;
            $jsonContent['message'] = $data['accept'] ? 'Prestation acceptée.' : 'Prestation déclinée.';
        }

        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
