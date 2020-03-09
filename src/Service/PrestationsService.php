<?php

namespace App\Service;

class PrestationsService
{
    public function setPrestations()
    {
        $token = bin2hex(random_bytes(32));

        return $token;
    }
}
