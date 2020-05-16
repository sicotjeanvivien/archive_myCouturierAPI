<?php

namespace App\Controller\api;

use App\Entity\UserPriceRetouching;
use App\Repository\RetouchingRepository;
use App\Repository\UserAppRepository;
use App\Repository\UserPriceRetouchingRepository;
use App\Service\MangoPayService;
use App\Service\PrestationsService;
use Doctrine\ORM\EntityManagerInterface;
use MangoPay\BankingAlias;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 *@Route("/api/userPriceRetouching") 
 */
class UserPriceRetouchingController extends AbstractController
{

    private $em;
    private $retouchingRepository;
    private $userAppRepository;
    private $userPriceRetouchingRepository;
    private $prestationsService;

    public function __construct(
        EntityManagerInterface $em,
        RetouchingRepository $retouchingRepository,
        UserAppRepository $userAppRepository,
        UserPriceRetouchingRepository $userPriceRetouchingRepository,
        PrestationsService $prestationsService,
        MangoPayService $mangoPayService
    ) {
        $this->em = $em;
        $this->mangoPayService = $mangoPayService;
        $this->retouchingRepository = $retouchingRepository;
        $this->userAppRepository = $userAppRepository;
        $this->userPriceRetouchingRepository = $userPriceRetouchingRepository;
        $this->prestationsService = $prestationsService;
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function show(Request $request)
    {

        $retouching = $this->retouchingRepository->findAll();
        $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
        $jsonContent = [];
        foreach ($retouching as $retouche) {
            $userPriceRetouching= $this->userPriceRetouchingRepository->findOneBy(['Retouching'=> $retouche, 'UserApp'=> $userApp]);
            dump($userPriceRetouching);
            $jsonContent[] = [
                'id' => !empty($retouche->getId()) ? $retouche->getId() : '',
                'CategoryRetouching' => !empty($retouche->getCategoryRetouching()->getType()) ? $retouche->getCategoryRetouching()->getType() : '',
                'type' => !empty($retouche->getType()) ? $retouche->getType() : '',
                'description' => !empty($retouche->getDescription()) ? $retouche->getDescription() : '',
                'supplyCost'=>!empty($userPriceRetouching) ? $userPriceRetouching->getSupplyCost():'',
                'daedline'=>!empty($userPriceRetouching) ?$userPriceRetouching->getDeadline():'',
                'tool'=> !empty($userPriceRetouching) ?$userPriceRetouching->getTool():'',
                'commitment'=> !empty($userPriceRetouching) ? $userPriceRetouching->getCommitment() : '',
                'value' => !empty($userPriceRetouching) ? strval($userPriceRetouching->getPriceCouturier()) : '',
            ];
        }
        $response = new Response;
        $response
            ->setContent(json_encode($jsonContent))
            ->headers->set('Content-Type', 'application/json');
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
            $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);

            $mangoBankAccount = $this->mangoPayService->setMangoBankAccount($userApp->getMangoUserId(), $userApp->getAddress(), $data['bankAccount']);
            $jsonContent['message'] = $mangoBankAccount;
            if (isset($mangoBankAccount->Errors)) {
                $jsonContent = [
                    'error' => true,
                    'message' => $mangoBankAccount,
                ];
                $response->setContent(json_encode($jsonContent));
                return $response;
            }

            $bankAccountList = json_decode($userApp->getMangoBankAccountId());
            $bankAccountList[] = $mangoBankAccount->Id;


            $userApp
                ->setMangoBankAccountId(json_encode($bankAccountList))
                ->setActiveCouturier($data['activeCouturier']);

            foreach ($data['userRetouchingPrice'] as $retouche) {
                if ($retouche['active']) {
                    $retouching = $this->retouchingRepository->findOneBy(['id' => $retouche['id']]);
                    $countUserPriceRetouching = $this->userPriceRetouchingRepository->countUserPriceRetouching($userApp, $retouching);
                    $userPriceRetouching =  $this->userPriceRetouchingRepository->findOneBy(['UserApp' => $userApp, 'Retouching' => $retouching]);
                    $priceClient = $this->prestationsService->calculPriceClient(intval($retouche['value']));
                    if (intval($countUserPriceRetouching) === 1) {
                        $userPriceRetouching
                            ->setPriceCouturier(empty($retouche['value']) ? 0 : intval($retouche['value']))
                            ->setTool(empty($retouche['tool']) ? null : intval($retouche['tool']))
                            ->setDeadline(empty($retouche['deadline']) ? null : intval($retouche['deadline']))
                            ->setCommitment(empty($retouche['commitment']) ? null : intval($retouche['commitment']))
                            ->setSupplyCost(empty($retouche['supplyCost']) ? null : intval($retouche['supplyCost']))
                            ->setPriceShowClient(intval($priceClient));
                    } else if (intval($countUserPriceRetouching) < 1) {
                        $newUserPriceRetouching = new UserPriceRetouching();
                        $newUserPriceRetouching
                            ->setRetouching($retouching)
                            ->setUserApp($userApp)
                            ->setPriceCouturier(empty($retouche['value']) ? 0 : intval($retouche['value']))
                            ->setTool(empty($retouche['tool']) ? null : intval($retouche['tool']))
                            ->setDeadline(empty($retouche['deadline']) ? null : intval($retouche['deadline']))
                            ->setCommitment(empty($retouche['commitment']) ? null : intval($retouche['commitment']))
                            ->setSupplyCost(empty($retouche['supplyCost']) ? null : intval($retouche['supplyCost']))
                            ->setPriceShowClient(intval($priceClient));
                        $this->em->persist($newUserPriceRetouching);
                    }
                }
            }
            $this->em->flush();

            $jsonContent['error'] = false;
            $jsonContent['message'] = 'information de profil mis à jour';
        }
        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
