<?php

namespace App\Service;

use App\Repository\UserAppRepository;

class UserAppService
{
    public function __construct(UserAppRepository $userAppRepository)
    {
        $this->userAppRepository = $userAppRepository;
    }

    public function validateDataAccount($data)
    {


        $error = [
            'error' => false,
            'message' => '',
        ];

        $id = empty($data['id']) ? null : rtrim(ltrim($data['id']));
        $firstname = rtrim(ltrim($data['firstname']));
        $lastname = rtrim(ltrim($data['lastname']));
        $bio = rtrim(ltrim($data['bio']));
        $email = rtrim(ltrim($data['email']));
        $emailConfirm = rtrim(ltrim($data['emailConfirm']));

        if (empty($firstname) || empty($lastname) ||empty($bio) ) {
            $error['error'] = true;
            $error['message'] = $error['message'] . 'champs vide';
        }
        if (
            empty($email) ||
            stristr($email, '@') === FALSE ||
            ($this->userAppRepository->countUserByEmail($email)) > 0
        ) {
            $error['error'] = true;
            $error['message'] = $error['message'] . ' email non valide';
        }
        if ($email !== $emailConfirm) {
            $error['error'] = true;
            $error['message'] = $error['message'] . 'les deux adresses emails ne correspondent pas';
        }
        return $error;
    }

    public function validateDataPassword($data)
    {
        $error = [
            'error' => false,
            'message' => '',
        ];
        $password = ltrim($data['password']);
        $passwordConfirm = ltrim($data['passwordConfirm']);

        if (empty($password) || empty($passwordConfirm)) {
            $error['message'] = $error['message'] . 'champ vide';
        }
        if ($password !== $passwordConfirm) {
            $error['message'] = $error['message'] . ' champs non identique';
        }
        if (strlen($password) < 7) {
            $error['message'] = $error['message'] . ' nombre de caratères insufissant';
        }
        return $error;
    }
}
