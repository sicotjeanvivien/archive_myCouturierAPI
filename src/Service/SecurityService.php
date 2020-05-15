<?php

namespace App\Service;

class SecurityService
{
    public function tokenGenerator()
    {
        $token = bin2hex(random_bytes(64));

        return $token;
    }

    public function passwordGenerator()
    {
        $password = '';
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < 8; ++$i) {
            $password .= $keyspace[random_int(0, $max)];
        }

        return $password;
    }

    public function codeConfirm()
    {
        $code = '';
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < 4; ++$i) {
            $code .= $keyspace[random_int(0, $max)];
        }

        return $code;
    }
}
