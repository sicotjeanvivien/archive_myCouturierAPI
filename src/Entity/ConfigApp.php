<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfigAppRepository")
 */
class ConfigApp
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $CGV;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $defaultCommission;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hostMailer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $usernameMailer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passwordMailer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portMailer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $protocoleMailer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adminEmail;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCGV(): ?string
    {
        return $this->CGV;
    }

    public function setCGV(?string $CGV): self
    {
        $this->CGV = $CGV;

        return $this;
    }

    public function getDefaultCommission(): ?int
    {
        return $this->defaultCommission;
    }

    public function setDefaultCommission(?int $defaultCommission): self
    {
        $this->defaultCommission = $defaultCommission;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(string $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getHostMailer(): ?string
    {
        return $this->hostMailer;
    }

    public function setHostMailer(?string $hostMailer): self
    {
        $this->hostMailer = $hostMailer;

        return $this;
    }

    public function getUsernameMailer(): ?string
    {
        return $this->usernameMailer;
    }

    public function setUsernameMailer(?string $usernameMailer): self
    {
        $this->usernameMailer = $usernameMailer;

        return $this;
    }

    public function getPasswordMailer(): ?string
    {
        return $this->passwordMailer;
    }

    public function setPasswordMailer(?string $passwordMailer): self
    {
        $this->passwordMailer = $passwordMailer;

        return $this;
    }

    public function getPortMailer(): ?string
    {
        return $this->portMailer;
    }

    public function setPortMailer(?string $portMailer): self
    {
        $this->portMailer = $portMailer;

        return $this;
    }

    public function getProtocoleMailer(): ?string
    {
        return $this->protocoleMailer;
    }

    public function setProtocoleMailer(?string $protocoleMailer): self
    {
        $this->protocoleMailer = $protocoleMailer;

        return $this;
    }

    public function getAdminEmail(): ?string
    {
        return $this->adminEmail;
    }

    public function setAdminEmail(?string $adminEmail): self
    {
        $this->adminEmail = $adminEmail;

        return $this;
    }
}
