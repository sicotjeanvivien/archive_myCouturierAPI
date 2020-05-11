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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $deadline;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tool;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $commitment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Prestations", mappedBy="userPriceRetouching")
     */
    private $prestations;

    public function __construct()
    {
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

    public function getDeadline(): ?int
    {
        return $this->deadline;
    }

    public function setDeadline(?int $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getTool(): ?string
    {
        return $this->tool;
    }

    public function setTool(?string $tool): self
    {
        $this->tool = $tool;

        return $this;
    }

    public function getCommitment(): ?bool
    {
        return $this->commitment;
    }

    public function setCommitment(?bool $commitment): self
    {
        $this->commitment = $commitment;

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
            $prestation->setUserPriceRetouching($this);
        }

        return $this;
    }

    public function removePrestation(Prestations $prestation): self
    {
        if ($this->prestations->contains($prestation)) {
            $this->prestations->removeElement($prestation);
            // set the owning side to null (unless already changed)
            if ($prestation->getUserPriceRetouching() === $this) {
                $prestation->setUserPriceRetouching(null);
            }
        }

        return $this;
    }

}
