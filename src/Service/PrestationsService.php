<?php

namespace App\Service;

use App\Entity\Prestations;
use App\Entity\PriceGrid;
use App\Repository\PrestationsRepository;
use App\Repository\PriceGridRepository;

class PrestationsService
{

    public $priceGridRepository;
    public $prestationsRepository;

    public function __construct(
        PriceGridRepository $priceGridRepository,
        PrestationsRepository $prestationsRepository
    ) {
        $this->prestationsRepository = $prestationsRepository;
        $this->priceGridRepository = $priceGridRepository;
    }

    public function calculPriceClient($priceCouturier)
    {
        $resultCommission = $this->priceGridRepository->findCommission($priceCouturier);
        $commission = $resultCommission === null ? PriceGrid::DEFAULTVALUE : $resultCommission['commission'];
        $price = $priceCouturier + $commission;
        return $price;
    }

    public function prestaClient($userApp)
    {
        dump($userApp);
        $client = [];
        $client['inProgress'] = $this->prestationsRepository->findPrestaByClientState($userApp, Prestations::ACTIVE);
        $client['end'] = $this->prestationsRepository->findPrestaByClientState($userApp, Prestations::INACTIVE);

        return $client;
    }

    public function prestaCouturier($userApp)
    {
        dump($userApp);
        $couturier = [];
        $couturier['inProgress'] = $this->prestationsRepository->findPrestaByCouturierState($userApp, Prestations::ACTIVE);
        $couturier['end'] = $this->prestationsRepository->findPrestaByCouturierState($userApp, Prestations::INACTIVE);
        return $couturier;
    }
}
