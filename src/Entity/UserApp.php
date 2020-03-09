<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Serializer;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserAppRepository")
 */
class UserApp implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"group1"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"group1"})
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Groups({"group1"})
     */
    private $apitoken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"group1"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"group1"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"group1"})
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserPriceRetouching", mappedBy="UserApp")
     * @MaxDepth(1)
     */
    private $userPriceRetouchings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Prestations", mappedBy="client")
     * @MaxDepth(1)
     */
    private $prestations;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"group1"})
     */
    private $privateMode;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"group1"})
     */
    private $imageProfil;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"group1"})
     */
    private $bio;

    public function __construct()
    {
        $this->userPriceRetouchings = new ArrayCollection();
        $this->prestations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getApitoken(): ?string
    {
        return $this->apitoken;
    }

    public function setApitoken(?string $apitoken): self
    {
        $this->apitoken = $apitoken;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|UserPriceRetouching[]
     */
    public function getUserPriceRetouchings(): Collection
    {
        return $this->userPriceRetouchings;
    }

    public function addUserPriceRetouching(UserPriceRetouching $userPriceRetouching): self
    {
        if (!$this->userPriceRetouchings->contains($userPriceRetouching)) {
            $this->userPriceRetouchings[] = $userPriceRetouching;
            $userPriceRetouching->setUserApp($this);
        }

        return $this;
    }

    public function removeUserPriceRetouching(UserPriceRetouching $userPriceRetouching): self
    {
        if ($this->userPriceRetouchings->contains($userPriceRetouching)) {
            $this->userPriceRetouchings->removeElement($userPriceRetouching);
            // set the owning side to null (unless already changed)
            if ($userPriceRetouching->getUserApp() === $this) {
                $userPriceRetouching->setUserApp(null);
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
            $prestation->setClient($this);
        }

        return $this;
    }

    public function removePrestation(Prestations $prestation): self
    {
        if ($this->prestations->contains($prestation)) {
            $this->prestations->removeElement($prestation);
            // set the owning side to null (unless already changed)
            if ($prestation->getClient() === $this) {
                $prestation->setClient(null);
            }
        }

        return $this;
    }

    public function serialize()
    {
        return serialize($this->id);
    }

    public function unserialize($serialized)
    {
        $this->id = unserialize($serialized);
    }

    public function getPrivateMode(): ?bool
    {
        return $this->privateMode;
    }

    public function setPrivateMode(?bool $privateMode): self
    {
        $this->privateMode = $privateMode;

        return $this;
    }

    public function getImageProfil(): ?string
    {
        return $this->imageProfil;
    }

    public function setImageProfil(?string $imageProfil): self
    {
        $this->imageProfil = $imageProfil;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }
}
