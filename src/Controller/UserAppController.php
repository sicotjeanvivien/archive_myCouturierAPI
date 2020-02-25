<?php

namespace App\Controller;

use App\Entity\UserApp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class UserAppController
{

    private $em;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/userapp_create", methods={"POST"})
     */
    public function userApp_create(Request $request)
    {
        $response = new Response();

        if (!empty($data = json_decode($request->getContent(), true)) && $request->headers->get('Content-Type', 'application/json')) {

            dump($request->getContent());
            dump(empty($data['password']));

            if (
                !empty($data['password'])
                && !empty($data['passwordConfirm'])
                && strlen($data['password']) > 7
                && $data['password'] === $data['passwordConfirm']
            ) {
                $user = $this->serializer->deserialize($request->getContent(), UserApp::class, 'json');
                $user->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    $data['password']
                ));
                $this->em->persist($user);
                $this->em->flush();
                dump($user);
            }
            //TODOO
            return $response;
        } else {
            dump('error');
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent('Error');
            return  $response;
        }
    }
}
