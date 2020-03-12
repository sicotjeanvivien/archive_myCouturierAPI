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
        $commission = $this->priceGridRepository->findCommission($priceCouturier) === null? PriceGrid::DEFAULTVALUE : $this->priceGridRepository->findCommission($priceCouturier);
        $price = $priceCouturier + $commission;
        return $price;
    }


}
