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
        
        $id = empty($data['id'])? null:ltrim($data['id']);
        $firstname = ltrim($data['firstname']);
        $lastname = ltrim($data['lastname']);
        $username = ltrim($data['username']);
        $email = ltrim($data['email']);

        if (empty($firstname)) {
            $error['error'] = true;
            $error['message'] = $error['message'] . 'prénom non valide';
        }
        if (
            empty($username)
            || $this->userAppRepository->countUsername($username, $id) > 0
        ) {
            $error['error'] = true;
            $error['message'] = $error['message'] . ' username non valide';
        }
        if (empty($lastname)) {
            $error['error'] = true;
            $error['message'] = $error['message'] . ' nom non valide';
        }
        if (
            empty($email)
            || stristr($email, '@') === FALSE
        ) {
            $error['error'] = true;
            $error['message'] = $error['message'] . ' email non valide';
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
