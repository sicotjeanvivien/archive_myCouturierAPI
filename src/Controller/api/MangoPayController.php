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
 *@Route("/api/mangopay") 
 */
class MangoPayController extends AbstractController
{
    private $userAppRepository;
    private $mangoPayService;
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
     * @Route("/listCard", methods={"GET"})
     */
    public function listCardForUser(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => false,
            'message' => 'server error',
        ];
        $user = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
        dump($this->mangoPayService->listCardForUser($user->getMangoUserId()));
        $jsonContent['listCard'] = $this->mangoPayService->listCardForUser($user->getMangoUserId());

        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/bankAccounts", methods={"GET"})
     */
    public function listBankAccounts(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => false,
            'message' => 'server error',
        ];
        $user = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
        dump($this->mangoPayService->listBankAccounts($user->getMangoUserId()));
        $jsonContent['bankAccounts'] = $this->mangoPayService->listBankAccounts($user->getMangoUserId());

        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/bankAccounts", methods={"POST"})
     */
    public function createBankAccounts(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => false,
            'message' => 'server error',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);

            $mangoBankAccount = $this->mangoPayService->setMangoBankAccount($userApp->getMangoUserId(), $userApp->getAddress(), $data['bankAccount']);
            $jsonContent['message'] = $mangoBankAccount;
            if (isset($mangoBankAccount->Errors)) {
                $jsonContent = [
                    'error' => true,
                    'bankAccount' => $mangoBankAccount,
                ];
                $response->setContent(json_encode($jsonContent));
                return $response;
            }
            $bankAccountList = json_decode($userApp->getMangoBankAccountId());
            $bankAccountList[] = $mangoBankAccount->Id;
            $userApp
                ->setMangoBankAccountId(json_encode($bankAccountList))
                ->setActiveCouturier($data['activeCouturier']);
            $this->em->flush();

            $jsonContent['error'] = false;
            $jsonContent['message'] = 'information de profil mis à jour';
        }

        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/bankAccounts", methods={"DELETE"})
     */
    public function deleteBankAccounts(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => false,
            'message' => 'server error',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
            $bankAccount = $this->mangoPayService->deactivateBankAccount($userApp->getMangoUserId() ,$data['bankAccountId']);
            dump($bankAccount);
            if (!$bankAccount->Active) {
                $jsonContent=[
                    'error'=> false,
                    'message'=> 'carte desactivé'
                ];
            }
        }

        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/card", methods={"GET"})
     */
    public function createTokenCardRegistration(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => false,
            'message' => 'server error',
        ];

        $user = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
        $token = $this->mangoPayService->createTokenCard($user->getMangoUserId());
        if (isset($token->Errors)) {
            $jsonContent = [
                'error' => true,
                'message' => $token,
            ];
            $response->setContent(json_encode($jsonContent));
            return $response;
        }
        $jsonContent['token'] = $token;

        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/card", methods={"PUT"})
     */
    public function putTokenDatas(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => false,
            'message' => 'server error',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $user = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
            $cardRegistration =  $this->mangoPayService->updateCardRegistration($data['RegistrationData'], $data['CardRegId']);
            $jsonContent['message'] = $cardRegistration;
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }
   
    /**
     * @Route("/card", methods={"DELETE"})
     */
    public function deleteCard(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'server error',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $card= $this->mangoPayService->deactivateCard($data['cardId']);
            if (!$card->Active) {
                $jsonContent=[
                    'error'=> false,
                    'message'=> 'carte desactivé'
                ];
            }
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }



    /**
     * @Route("/payInCardDirect", methods={"POST"})
     */
    public function payInCardDirect(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'server error',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $prestation = $this->prestationsRepository->findOneBy(['id' => $data['prestation']]);
            $client = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);

            $mangoUserId = $client->getMangoUserId();
            $mangoWalletId = $client->getMangoWalletId();
            $mangoCardId = $data['cardId'];
            $debit = ($prestation->getUserPriceRetouching()->getPriceShowClient());
            $fees = ($prestation->getUserPriceRetouching()->getPriceShowClient() - $prestation->getUserPriceRetouching()->getPriceCouturier());
            $urlReturn = $this->generateUrl('3Dsecure');

            $payInCardDirect = $this->mangoPayService->payInCardDirect($mangoUserId, $mangoWalletId, $mangoCardId, $debit, $fees, $urlReturn);

            if (isset($payInCardDirect->Status)) {
                $prestation->setPay(true);
                $statut = $this->statutHistoryRepository->findOneBy(['statut' => StatutHistory::PAY]);
                $presatationHistory = new PrestationHistory();
                $presatationHistory
                    ->setPrestation($prestation)
                    ->setStatut(isset($statut) ? $statut : null)
                    ->setDate(new DateTime('NOW'));
                $this->em->persist($presatationHistory);
                $this->em->flush();
                $jsonContent = [
                    'error' => false,
                    'message' => $payInCardDirect->ResultMessage,
                    'prestation' => ['id' => $prestation->getId()]
                ];
            }
            if (isset($payInCardDirect->Errors)) {
                $jsonContent['message'] = $payInCardDirect->Errors;
            }
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/3Dsecure", name="3Dsecure", methods={"POST"})
     */
    public function secure3D(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => false,
            'message' => $request,
        ];

        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     * @Route("/wallet", methods={"GET"})
     */
    public function showWallet(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => false,
            'message' => 'server error',
        ];
        $user = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
        dump($this->mangoPayService->getWallet($user->getMangoWalletId()));

        $jsonContent['wallet'] = $this->mangoPayService->getWallet($user->getMangoWalletId());
        $response->setContent(json_encode($jsonContent));
        return $response;
    }

    /**
     *@Route("/payOutBankWire", methods={"POST"})  
     */
    public function payOutBankWire(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'server error',
        ];
        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {
            $client = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);

            $mangoUserId = $client->getMangoUserId();
            $mangoWalletId = $client->getMangoWalletId();
            $mangoBankAccountId = $data['bankAccountId'];
            $debitAmout = $data['debitAmount'];
            $payOut = $this->mangoPayService->payOutBankWire($mangoUserId, $mangoWalletId, $debitAmout, $mangoBankAccountId);

            $jsonContent['payOut']= $payOut;

            if ($payOut->Status === \MangoPay\PayOutStatus::Succeeded) {
                $jsonContent = [
                    'error' => false,
                    'message' => 'Virement effectué.',
                ];
            }
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
