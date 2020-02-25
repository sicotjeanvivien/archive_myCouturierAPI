<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserPriceRetouchingRepository")
 */
class UserPriceRetouching
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserApp", inversedBy="userPriceRetouchings")
     */
    private $UserApp;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Retouching", inversedBy="userPriceRetouchings")
     */
    private $Retouching;

    /**
     * @ORM\Column(type="integer")
     */
    private $PriceCouturier;

    /**
     * @ORM\Column(type="integer")
     */
    private $PriceShowClient;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PrestationHistory", mappedBy="retouching")
     */
    private $prestationHistories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Prestations", mappedBy="retouching")
     */
    private $prestations;

    public function __construct()
    {
        $this->prestationHistories = new ArrayCollection();
        $this->prestations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserApp(): ?UserApp
    {
        return $this->UserApp;
    }

    public function setUserApp(?UserApp $UserApp): self
    {
        $this->UserApp = $UserApp;

        return $this;
    }

    public function getRetouching(): ?Retouching
    {
        return $this->Retouching;
    }

    public function setRetouching(?Retouching $Retouching): self
    {
        $this->Retouching = $Retouching;

        return $this;
    }

    public function getPriceCouturier(): ?int
    {
        return $this->PriceCouturier;
    }

    public function setPriceCouturier(int $PriceCouturier): self
    {
        $this->PriceCouturier = $PriceCouturier;

        return $this;
    }

    public function getPriceShowClient(): ?int
    {
        return $this->PriceShowClient;
    }

    public function setPriceShowClient(int $PriceShowClient): self
    {
        $this->PriceShowClient = $PriceShowClient;

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
            $prestationHistory->setRetouching($this);
        }

        return $this;
    }

    public function removePrestationHistory(PrestationHistory $prestationHistory): self
    {
        if ($this->prestationHistories->contains($prestationHistory)) {
            $this->prestationHistories->removeElement($prestationHistory);
            // set the owning side to null (unless already changed)
            if ($prestationHistory->getRetouching() === $this) {
                $prestationHistory->setRetouching(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Prestations[]
     */
    public function getPrestations(): Collection
    {
        return $this->prestations;
    }

    public function addPrestation(Prestations $prestation): self
    {
        if (!$this->prestations->contains($prestation)) {
            $this->prestations[] = $prestation;
            $prestation->setRetouching($this);
        }

        return $this;
    }

    public function removePrestation(Prestations $prestation): self
    {
        if ($this->prestations->contains($prestation)) {
            $this->prestations->removeElement($prestation);
            // set the owning side to null (unless already changed)
            if ($prestation->getRetouching() === $this) {
                $prestation->setRetouching(null);
            }
        }

        return $this;
    }
}
