<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrestationsRepository")
 */
class Prestations
{

    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';
    public const STANDBY = 'standby';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserApp", inversedBy="prestations")
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PrestationHistory", mappedBy="prestation")
     */
    private $prestationHistories;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $accept;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pay;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserPriceRetouching", inversedBy="prestations")
     */
    private $userPriceRetouching;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="prestation")
     */
    private $messages;

    public function __construct()
    {
        $this->prestationHistories = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?UserApp
    {
        return $this->client;
    }

    public function setClient(?UserApp $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|PrestationHistory[]
     */
    public function getPrestationHistories(): Collection
    {
        return $this->prestationHistories;
    }

    public function addPrestationHistory(PrestationHistory $prestationHistory): self
    {
        if (!$this->prestationHistories->contains($prestationHistory)) {
            $this->prestationHistories[] = $prestationHistory;
            $prestationHistory->setPrestation($this);
        }

        return $this;
    }

    public function removePrestationHistory(PrestationHistory $prestationHistory): self
    {
        if ($this->prestationHistories->contains($prestationHistory)) {
            $this->prestationHistories->removeElement($prestationHistory);
            // set the owning side to null (unless already changed)
            if ($prestationHistory->getPrestation() === $this) {
                $prestationHistory->setPrestation(null);
            }
        }

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getAccept(): ?bool
    {
        return $this->accept;
    }

    public function setAccept(?bool $accept): self
    {
        $this->accept = $accept;

        return $this;
    }

    public function getPay(): ?bool
    {
        return $this->pay;
    }

    public function setPay(?bool $pay): self
    {
        $this->pay = $pay;

        return $this;
    }

    public function getUserPriceRetouching(): ?UserPriceRetouching
    {
        return $this->userPriceRetouching;
    }

    public function setUserPriceRetouching(?UserPriceRetouching $userPriceRetouching): self
    {
        $this->userPriceRetouching = $userPriceRetouching;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setPrestation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getPrestation() === $this) {
                $message->setPrestation(null);
            }
        }

        return $this;
    }

}
