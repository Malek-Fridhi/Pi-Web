<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\InscriptionevenementRepository;

#[ORM\Entity(repositoryClass: InscriptionevenementRepository::class)]
#[ORM\Table(name: 'inscriptionevenement')]
class Inscriptionevenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $idinscriptionevenement = null;

    public function getIdinscriptionevenement(): ?int
    {
        return $this->idinscriptionevenement;
    }

    public function setIdinscriptionevenement(int $idinscriptionevenement): self
    {
        $this->idinscriptionevenement = $idinscriptionevenement;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: 'inscriptionevenements')]
    #[ORM\JoinColumn(name: 'idevenement', referencedColumnName: 'idevenement')]
    #[Assert\NotNull(message: "Un événement doit être associé")]
    private ?Evenement $evenement = null;

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): self
    {
        $this->evenement = $evenement;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'inscriptionevenements')]
    #[ORM\JoinColumn(name: 'iduser', referencedColumnName: 'id')]
    #[Assert\NotNull(message: "Un utilisateur doit être associé")]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotNull(message: "La date d'inscription est obligatoire")]
    #[Assert\LessThanOrEqual(
        "now",
        message: "La date d'inscription ne peut pas être dans le futur"
    )]
    private ?\DateTimeInterface $date_inscription = null;

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(?\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "Le statut est obligatoire")]
    #[Assert\Choice(
        choices: ['Pending', 'Approved', 'Canceled', 'Waitlist'],
        message: "Statut d'inscription non valide"
    )]
    private ?string $statut = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    // Alias pour compatibilité
    public function getDate_inscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDate_inscription(?\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;
        return $this;
    }
}