<?php

namespace App\Service;

use App\Repository\ConfigAppRepository;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailerInterface;
    private $configAppRepository;

    public function __construct(MailerInterface $mailerInterface, ConfigAppRepository $configAppRepository)
    {
        $this->mailerInterface = $mailerInterface;
        $this->configAppRepository = $configAppRepository;
    }

    public function sendEmail($to, $subject, $content)
    {
        $transport = $this->createTransportCustom();
        $adminMail = $this->configAppRepository->findOneBy(['site' => $_ENV['SITE']])->getUsernameMailer();
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from($adminMail)
            ->to($to)
            ->subject($subject)
            ->html($content);
        $mailer->send($email);
    }

    private function createTransportCustom()
    {
        $configApp = $this->configAppRepository->findOneBy(['site' => $_ENV['SITE']]);
        $host = $configApp->getHostMailer();
        $port = $configApp->getPortMailer();
        $password = $configApp->getPasswordMailer();
        $username = $configApp->getUsernameMailer();
        $protocole = $configApp->getProtocoleMailer();
        if (!empty($host) && !empty($port) && !empty($password) && !empty($username) && !empty($protocole)) {
            $dsn = $protocole . '://' . $username . ':' . $password . '@' . $host . ':' . $port;
        } else {
            $dsn = 'smtp://admin@mycouturierapi.sicot-development.fr:Azertyuiop123!@smtp.ionos.fr:465';
        }
        $transport = Transport::fromDsn($dsn);
        return $transport;
    }
}
