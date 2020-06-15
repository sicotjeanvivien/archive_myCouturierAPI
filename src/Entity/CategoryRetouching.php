<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRetouchingRepository")
 */
class CategoryRetouching
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Retouching", mappedBy="CategoryRetouching")
     */
    private $retouchings;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showHidden;

    public function __construct()
    {
        $this->retouchings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Retouching[]
     */
    public function getRetouchings(): Collection
    {
        return $this->retouchings;
    }

    public function addRetouching(Retouching $retouching): self
    {
        if (!$this->retouchings->contains($retouching)) {
            $this->retouchings[] = $retouching;
            $retouching->setCategoryRetouching($this);
        }

        return $this;
    }

    public function removeRetouching(Retouching $retouching): self
    {
        if ($this->retouchings->contains($retouching)) {
            $this->retouchings->removeElement($retouching);
            // set the owning side to null (unless already changed)
            if ($retouching->getCategoryRetouching() === $this) {
                $retouching->setCategoryRetouching(null);
            }
        }

        return $this;
    }

    public function getShowHidden(): ?bool
    {
        return $this->showHidden;
    }

    public function setShowHidden(?bool $showHidden): self
    {
        $this->showHidden = $showHidden;

        return $this;
    }
}
