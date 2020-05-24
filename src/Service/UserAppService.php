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

        $id = empty($data['id']) ? 0 : rtrim(ltrim($data['id']));
        $firstname = empty($data['firstname']) ? null : rtrim(ltrim( $data['firstname']));
        $lastname = empty($data['lastname']) ? null : rtrim(ltrim($data['lastname']));
        $bio = !empty($data['bio']) ? rtrim(ltrim($data['bio'])) : null;
        $email = empty($data['email']) ? null : rtrim(ltrim($data['email']));
        $emailConfirm = empty($data['emailConfirm']) ? null : rtrim(ltrim($data['emailConfirm']));

        if (!isset($firstname)) {
            $error['error'] = true;
            $error['message'] = $error['message'] . 'champs vide nom';
        }
        if (!isset($lastname)) {
            $error['error'] = true;
            $error['message'] = $error['message'] . 'champs vide prénon';
        }
        // dump($this->userAppRepository->countUserByEmail($email, $id));
        if (
            !isset($email) ||
            stristr($email, '@') === FALSE ||
            ($this->userAppRepository->countUserByEmail($email, $id)) > 0
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
