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
}
