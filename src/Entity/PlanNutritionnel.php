<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlanNutritionnelRepository::class)]
#[ORM\Table(name: 'planNutritionnel')]
class PlanNutritionnel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idPlan', type: 'integer')]
    private ?int $idPlan = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'planNutritionnels')]
    #[ORM\JoinColumn(name: 'idUser', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Objectif::class, inversedBy: 'planNutritionnels')]
    #[ORM\JoinColumn(name: 'idObjectif', referencedColumnName: 'idObjectif')]
    private ?Objectif $objectif = null;

    #[ORM\Column(name: 'dateCreation', type: 'date', nullable: false)]
    #[Assert\NotNull(message: "La date de création ne peut pas être nulle.")]
    #[Assert\Type("\DateTimeInterface", message: "La date de création doit être une date valide.")]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(name: 'dateModification', type: 'date', nullable: false)]
    #[Assert\NotNull(message: "La date de modification ne peut pas être nulle.")]
    #[Assert\Type("\DateTimeInterface", message: "La date de modification doit être une date valide.")]
    private ?\DateTimeInterface $dateModification = null;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotNull(message: "Le régime ne peut pas être nul.")]
    #[Assert\Length(
        min: 4,
        minMessage: "Le régime doit contenir au moins {{ limit }} caractères.",
        max: 1000,
        maxMessage: "Le régime ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $regime = null;

    #[ORM\Column(name: 'nbrJours', type: 'integer', nullable: false)]
    #[Assert\NotNull(message: "Le nombre de jours ne peut pas être nul.")]
    #[Assert\Positive(message: "Le nombre de jours doit être un entier positif.")]
    #[Assert\GreaterThanOrEqual(
        value: 1,
        message: "Le nombre de jours doit être supérieur ou égal à 1."
    )]
    private ?int $nbrJours = null;

    // Getters and Setters
    public function getIdPlan(): ?int
    {
        return $this->idPlan;
    }

    public function setIdPlan(int $idPlan): self
    {
        $this->idPlan = $idPlan;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getObjectif(): ?Objectif
    {
        return $this->objectif;
    }

    public function setObjectif(?Objectif $objectif): self
    {
        $this->objectif = $objectif;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;
        return $this;
    }

    public function getRegime(): ?string
    {
        return $this->regime;
    }

    public function setRegime(string $regime): self
    {
        $this->regime = $regime;
        return $this;
    }

    public function getNbrJours(): ?int
    {
        return $this->nbrJours;
    }

    public function setNbrJours(int $nbrJours): self
    {
        $this->nbrJours = $nbrJours;
        return $this;
    }
}