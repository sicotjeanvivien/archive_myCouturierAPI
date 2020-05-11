<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentaryRepository")
 */
class Commentary
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserApp", inversedBy="commentaries")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserApp", inversedBy="commentaries")
     */
    private $couturier;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?UserApp
    {
        return $this->author;
    }

    public function setAuthor(?UserApp $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCouturier(): ?UserApp
    {
        return $this->couturier;
    }

    public function setCouturier(?UserApp $couturier): self
    {
        $this->couturier = $couturier;

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

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
