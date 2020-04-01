<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactUsRepository")
 */
class ContactUs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserApp", inversedBy="contactUs")
     */
    private $userApp;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $response;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserApp(): ?UserApp
    {
        return $this->userApp;
    }

    public function setUserApp(?UserApp $userApp): self
    {
        $this->userApp = $userApp;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(string $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }
}
