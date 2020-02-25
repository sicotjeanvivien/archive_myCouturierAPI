<?php

namespace App\Service;

class TokenService
{
    public function tokenGenerator()
    {
        $token = bin2hex(random_bytes(32));

        return $token;
    }
}
