<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $titre = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull()]
    private ?\DateTimeInterface $date_echeance = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull()]
    private ?string $priorite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull()]
    private ?string $statut = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull()]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Getters and setters for each property...
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDateEcheance(): ?\DateTimeInterface
    {
        return $this->date_echeance;
    }

    public function setDateEcheance(\DateTimeInterface $date_echeance): static
    {
        $this->date_echeance = $date_echeance;

        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(string $priorite): static
    {
        $this->priorite = $priorite;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
