<?php

namespace App\Service;

use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;

class MailerService
{
    public function sendEmail(\Swift_mailer $mailer)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('recipient@example.com')
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'emails/registration.html.twig',
                    ['name' => $name]
                ),
                'text/html'
            )

            // you can remove the following code if you don't define a text version for your emails
            ->addPart(
                $this->renderView(
                    // templates/emails/registration.txt.twig
                    'emails/registration.txt.twig',
                    ['name' => $name]
                ),
                'text/plain'
            );

        $mailer->send($message);
    }
}
