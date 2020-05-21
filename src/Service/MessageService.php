<?php

namespace App\Service;

use App\Repository\PrestationsRepository;
use App\Repository\UserAppRepository;
use DateTime;

class MessageService
{

    private $userAppRepository;
    private $prestationsRepository;

    public function __construct(
        UserAppRepository $userAppRepository,
        PrestationsRepository $prestationsRepository
    ) {
        $this->prestationsRepository = $prestationsRepository;
        $this->userAppRepository = $userAppRepository;
    }

    public function set($message, $data, $author)
    {
        $author =  $this->userAppRepository->findOneBy(['apitoken' => $author]);
        $prestation = $this->prestationsRepository->findOneBy(['id' => $data['prestation']]);
        if (
            !empty($author)
            && !empty($prestation)
            && !empty($data['message'])
            && !empty($message)
        ) {
            $message
                ->setAuthor($author)
                ->setPrestation($prestation)
                ->setEditedDate(new DateTime('now'))
                ->setMessage($data['message']);
            return $message;
        }
        return $message = false;
    }
}
