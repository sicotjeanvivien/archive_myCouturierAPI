<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

// require_once __DIR__.'/vendor/autoload.php';

class NotificationPushService
{

    const host_Api_Expo_Push = "https://exp.host/--/api/v2/push/send";

    public function send_curl(string $recipient, array $message)
    {
        $client = HttpClient::create([
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        $response = $client->request('POST', self::host_Api_Expo_Push, [
            'json' => $message
        ]);
        return $response;
    }

    public function pushNewDemande($recipient)
    {
        $message = [
            "to" => $recipient,
            "sound" => "default",
            "priority"=> 'normal',
            "title" => "Nouvelle Demande",
            "body" => "Vous avez une nouvelle demande prestation.",
        ];
        $response = $this->send_curl($recipient, $message);
        return $response;
    }
    
    public function pushAccept($recipient)
    {
        $message = [
            "to" => $recipient,
            "sound" => "default",
            "actionId" => "requiredAuthenticationButton",
            "title" => "Prestation acceptée",
            "body" => "Votre demande de prestation vient d'être acceptée. Il vous reste à payer pour continuer.",
        ];
        $response = $this->send_curl($recipient, $message);
        return $response;
    }

    public function pushDecline($recipient)
    {
        $message = [
            "to" => $recipient,
            "sound" => "default",
            "actionId" => "requiredAuthenticationButton",
            "title" => "Prestation déclinée",
            "body" => "Votre demande de prestation vient d'être déclinée. Faite une nouvelle demande à un couturier.",
        ];
        $response = $this->send_curl($recipient, $message);
        return $response;
    }

    public function pushNewMessage($recipient)
    {
        $message = [
            "to" => $recipient,
            "sound" => "default",
            "actionId" => "requiredAuthenticationButton",
            "title" => "Nouveau message",
            "body" => "Vous avez un nouveau message.",
        ];
        $response = $this->send_curl($recipient, $message);
        return $response;
    }
}
