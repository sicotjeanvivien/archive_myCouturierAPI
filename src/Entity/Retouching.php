<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RetouchingRepository")
 */
class Retouching
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CategoryRetouching", inversedBy="retouchings")
     */
    private $CategoryRetouching;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $supplyQuestion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $supplyOption;

    public function __construct()
    {
       
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryRetouching(): ?CategoryRetouching
    {
        return $this->CategoryRetouching;
    }

    public function setCategoryRetouching(?CategoryRetouching $CategoryRetouching): self
    {
        $this->CategoryRetouching = $CategoryRetouching;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSupplyQuestion(): ?string
    {
        return $this->supplyQuestion;
    }

    public function setSupplyQuestion(?string $supplyQuestion): self
    {
        $this->supplyQuestion = $supplyQuestion;

        return $this;
    }

    public function getSupplyOption(): ?string
    {
        return $this->supplyOption;
    }

    public function setSupplyOption(?string $supplyOption): self
    {
        $this->supplyOption = $supplyOption;

        return $this;
    }

}
