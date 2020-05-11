<?php

namespace App\Service;

use MangoPay;


class MangoPayService
{

    private $mangoPayApi;

    public function __construct()
    {
        $this->mangoPayApi = new MangoPay\MangoPayApi();
        $this->mangoPayApi->Config->ClientId = 'sicotdev';
        $this->mangoPayApi->Config->ClientPassword = 'xvU4ifQbOLGMGxtM5R36wd82N7xGWsuhW0g3ncSce5emj5f9pN';
        $this->mangoPayApi->Config->TemporaryFolder = '../var/cache/';
        $this->mangoPayApi->Config->BaseUrl = 'https://api.sandbox.mangopay.com';
    }

    /**
     * Create Mangopay User
     * @return MangopPayUser $mangoUser
     */
    public function setMangoUser($data)
    {
        if (
            !empty($data['firstname']) &&
            !empty($data['lastname']) &&
            !empty($data['birthday']) &&
            !empty($data['email'])
        ) {
            $mangoUser = new \MangoPay\UserNatural();
            $mangoUser->PersonType = "NATURAL";
            $mangoUser->FirstName = rtrim(ltrim($data['firstname']));
            $mangoUser->LastName = rtrim(ltrim($data['lastname']));
            $mangoUser->Birthday = $data['birthday'];
            $mangoUser->Nationality = "FR";
            $mangoUser->CountryOfResidence = "FR";
            $mangoUser->Email = rtrim(ltrim($data['email']));

            //Send the request
            $mangoUser = $this->mangoPayApi->Users->Create($mangoUser);
            return $mangoUser;
        }
    }

    /**
     * Create Mangopay Wallet
     * @return MangoPayWallet $mangoWallet
     */
    public function setMangoWallet($mangoUserId)
    {
        $mangoWallet = new \MangoPay\Wallet();
        $mangoWallet->Owners = [$mangoUserId];
        $mangoWallet->Currency = "EUR";
        $mangoWallet->Description = "A very cool wallet";

        //Send the request
        $mangoWallet = $this->mangoPayApi->Wallets->Create($mangoWallet);
        return $mangoWallet;
    }
}
