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
use App\Service\NotificationPushService;
use App\Service\PrestationsService;
use App\Service\SecurityService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;

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
    private $securityService;
    private $mangoPayService;
    private $notificationPushService;
    private $em;

    public function __construct(
        NotificationPushService $notificationPushService,
        StatutHistoryRepository $statutHistoryRepository,
        UserAppRepository $userAppRepository,
        PrestationsRepository $prestationsRepository,
        RetouchingRepository $retouchingRepository,
        UserPriceRetouchingRepository $userPriceRetouchingRepository,
        EntityManagerInterface $entityManagerInterface,
        MangoPayService $mangoPayService,
        PrestationsService $prestationService,
        PrestationHistoryRepository $prestationHistoryRepository,
        MessageRepository $messageRepository,
        SecurityService $securityService
    ) {
        $this->em = $entityManagerInterface;
        $this->securityService = $securityService;
        $this->mangoPayService = $mangoPayService;
        $this->notificationPushService = $notificationPushService;
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
            $jsonContent['client'] = $this->prestationService->prestaClient($userApp->getId());
            $jsonContent['couturier'] = $this->prestationService->prestaCouturier($userApp->getId());
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
                $expoPush = $this->notificationPushService->pushNewDemande($userPriceRetouching->getUserApp()->getPushNotificationToken());
                $jsonContent['notification'] = $expoPush;
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
            if ($prestation->getAccept()) {
                $expoPush = $this->notificationPushService->pushAccept($prestation->getClient()->getPushNotificationToken());
            }if (!$prestation->getAccept()) {
                $expoPush = $this->notificationPushService->pushDecline($prestation->getClient()->getPushNotificationToken());
            }
            $prestation->setState($data['accept'] ? Prestations::ACTIVE : Prestations::INACTIVE);
            $statut = $this->statutHistoryRepository->findOneBy(['statut' => StatutHistory::ACCEPT]);
            $prestationHistory = new PrestationHistory();
            $prestationHistory
                ->setDate(new DateTime('now'))
                ->setStatut(isset($statut) ? $statut : null)
                ->setPrestation($prestation);
            $this->em->persist($prestationHistory);
            $this->em->flush();
            $jsonContent['notification'] = $expoPush;
            $jsonContent['error'] = false;
            $jsonContent['message'] = $data['accept'] ? 'Prestation acceptée.' : 'Prestation déclinée.';
        }

        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/congirmCode/{id}", methods={"GET"})
     */
    public function showConfirmCode(Request $request, $id)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => false,
            'message' => 'error server',
        ];
        $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
        $prestation = $this->prestationsRepository->findOneBy(['id' => $id]);
        $code = $prestation->getCodeConfirm() !== null ? $prestation->getCodeConfirm() : $this->securityService->codeConfirm();
        $prestation->setCodeConfirm($code);
        $this->em->flush();

        $jsonContent['code'] = $code;
        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/congirmCode", methods={"POST"})
     */
    public function confirmCode(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $prestation = $this->prestationsRepository->findOneBy(['id' => $data['prestationId']]);
            $jsonContent['transfer'] = empty($prestation->getMangoPayTransferId());
            $codePresta = $prestation->getCodeConfirm();
            $jsonContent['message'] = 'code invalide';
            if ($codePresta === $data['code'] && $prestation->getState() === Prestations::ACTIVE) {

                $mangoPayTransfert = $this->mangoPayService->transfer(
                    $prestation->getClient()->getMangoUserId(),
                    $prestation->getUserPriceRetouching()->getPriceCouturier(),
                    0,
                    $prestation->getClient()->getMangoWalletId(),
                    $prestation->getUserPriceRetouching()->getUserApp()->getMangoWalletId()
                );
                $jsonContent['transfer'] = $mangoPayTransfert;

                if ($mangoPayTransfert->Status === \MangoPay\TransactionStatus::Succeeded) {
                    $statut = $this->statutHistoryRepository->findOneBy(['statut' => StatutHistory::FINISHED]);
                    $prestation->setMangoPayTransferId($mangoPayTransfert->Id)->setState(Prestations::INACTIVE);

                    $prestationHistory = new PrestationHistory();
                    $prestationHistory
                        ->setDate(new DateTime('now'))
                        ->setStatut(isset($statut) ? $statut : null)
                        ->setPrestation($prestation);
                    $this->em->persist($prestationHistory);
                    $this->em->flush();

                    $jsonContent = [
                        'error' => false,
                        'message' => 'code valide',
                        'transfer' => $mangoPayTransfert
                    ];
                }
            }
        }

        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
