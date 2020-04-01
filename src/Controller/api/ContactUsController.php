<?php

namespace App\Controller\api;

use App\Entity\ContactUs;
use App\Repository\ConfigAppRepository;
use App\Repository\ContactUsRepository;
use App\Repository\UserAppRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;


class ContactUsController extends AbstractController
{
    private $em;
    private $conctatUsRepository;
    private $userAppRepository;
    private $mailerService;
    private $configAppRepository;

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        UserAppRepository $userAppRepository,
        ContactUsRepository $contactUsRepository,
        MailerService $mailerService,
        ConfigAppRepository $configAppRepository
    ) {
        $this->contactUsRepository = $contactUsRepository;
        $this->em = $entityManagerInterface;
        $this->userAppRepository = $userAppRepository;
        $this->mailerService = $mailerService;
        $this->configAppRepository = $configAppRepository;
    }

    /**
     * @Route("/api/ContactUs", methods={"POST"})
     */
    public function contactUs(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = [
            'error' => true,
            'message' => 'error server',
        ];

        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type') === 'application/json') {

            if (!empty($contentContactUs = $data['content']) && !empty($subject = $data['subject'])) {
                $adminMail = $this->configAppRepository->findOneBy(['site' => $_ENV['SITE']])->getAdminEmail();
                $userApp = $this->userAppRepository->findOneBy(['apitoken' => $request->headers->get('X-AUTH-TOKEN')]);
                $contactUs = new ContactUs();
                $contactUs
                    ->setMessage($contentContactUs)
                    ->setUserApp($userApp)
                    ->setSubject($subject);
                $this->em->persist($contactUs);
                $this->em->flush();
                //TODO template email notification
                $subjectMail = 'notification envoie de email';
                $contentMail = $this->renderView('emails/notificationContactUs.html.twig', [
                    'content' => $contentContactUs,
                    'subject' => $subject,
                ]);

                if (!(stristr($userApp->getEmail(), '@') === false)) {
                    $this->mailerService->sendEmail($userApp->getEmail(), $subjectMail, $contentMail);
                    $this->mailerService->sendEmail($adminMail, $subjectMail, $contentMail);
                    $jsonContent['message'] = 'email bien envoyé';
                    $jsonContent['error'] = false;
                }
            } else {
                $jsonContent['message'] = 'erreur dans la saisi de données';
            }
        }

        $response->setContent(json_encode($jsonContent));
        return $response;
    }
}
