<?php

namespace App\Service;

use App\Entity\PriceGrid;
use App\Repository\PriceGridRepository;

class PrestationsService
{

    public $priceGridRepository;

    public function __construct(PriceGridRepository $priceGridRepository)
    {
        $this->priceGridRepository = $priceGridRepository;
    }

    public function calculPriceClient($priceCouturier)
    { 
        $resultCommission = $this->priceGridRepository->findCommission($priceCouturier);
        $commission = $resultCommission === null? PriceGrid::DEFAULTVALUE : $resultCommission['commission'];
        $price = $priceCouturier + $commission;
        return $price;
    }


}
