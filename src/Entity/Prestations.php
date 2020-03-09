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
     * @ORM\ManyToOne(targetEntity="App\Entity\UserPriceRetouching", inversedBy="prestations")
     */
    private $retouching;

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

    public function __construct()
    {
        $this->prestationHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRetouching(): ?UserPriceRetouching
    {
        return $this->retouching;
    }

    public function setRetouching(?UserPriceRetouching $retouching): self
    {
        $this->retouching = $retouching;

        return $this;
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
        return $this->active;
    }

    public function setState(?string $active): self
    {
        $this->active = $active;

        return $this;
    }

}
