<?php

namespace App\Service;

use DateTime;

class MessageService
{
    public function set($message, $data, $author)
    {
        $author =  $this->userAppRepository->findOneBy(['apitoken' => $author]);
        $prestation = $this->prestationsRespository->findOneBy(['id' => $data['prestation']]);
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
