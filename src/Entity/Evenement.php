<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\EvenementRepository;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[ORM\Table(name: 'Evenement')]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $idevenement = null;

    public function getIdevenement(): ?int
    {
        return $this->idevenement;
    }

    public function setIdevenement(int $idevenement): self
    {
        $this->idevenement = $idevenement;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide")]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: "Le titre doit faire au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $titre = null;

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 2000,
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotNull(message: "La date est obligatoire")]
    #[Assert\GreaterThan(
        "today",
        message: "La date doit être dans le futur"
    )]
    private ?\DateTimeInterface $date = null;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\PositiveOrZero(message: "La durée doit être un nombre positif")]
    #[Assert\LessThanOrEqual(
        value: 1440,
        message: "La durée ne peut pas dépasser 1440 minutes (24h)"
    )]
    private ?int $duree = null;

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\PositiveOrZero(message: "La capacité doit être un nombre positif ou zéro")]
    private ?int $capacite = null;

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(?int $capacite): self
    {
        $this->capacite = $capacite;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "Le statut est obligatoire")]
    #[Assert\Choice(
        choices: ['Planned', 'Ongoing', 'Completed', 'Canceled'],
        message: "Statut non valide"
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'evenements')]
    #[ORM\JoinColumn(name: 'cree_par', referencedColumnName: 'id')]
    #[Assert\NotNull(message: "Un créateur doit être associé")]
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

    #[ORM\Column(name: 'imageUrl', type: 'string', nullable: true)]
    #[Assert\Url(message: "L'URL de l'image n'est pas valide")]
    #[Assert\Length(max: 255)]
    private ?string $imageUrl = null;

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Feedbackevenement::class, mappedBy: 'evenement')]
    private Collection $feedbackevenements;

    public function __construct()
    {
        $this->feedbackevenements = new ArrayCollection();
        $this->inscriptionevenements = new ArrayCollection();
    }

    /**
     * @return Collection<int, Feedbackevenement>
     */
    public function getFeedbackevenements(): Collection
    {
        return $this->feedbackevenements;
    }

    public function addFeedbackevenement(Feedbackevenement $feedbackevenement): self
    {
        if (!$this->feedbackevenements->contains($feedbackevenement)) {
            $this->feedbackevenements->add($feedbackevenement);
            $feedbackevenement->setEvenement($this);
        }
        return $this;
    }

    public function removeFeedbackevenement(Feedbackevenement $feedbackevenement): self
    {
        if ($this->feedbackevenements->removeElement($feedbackevenement)) {
            if ($feedbackevenement->getEvenement() === $this) {
                $feedbackevenement->setEvenement(null);
            }
        }
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Inscriptionevenement::class, mappedBy: 'evenement')]
    private Collection $inscriptionevenements;

    /**
     * @return Collection<int, Inscriptionevenement>
     */
    public function getInscriptionevenements(): Collection
    {
        return $this->inscriptionevenements;
    }

    public function addInscriptionevenement(Inscriptionevenement $inscriptionevenement): self
    {
        if (!$this->inscriptionevenements->contains($inscriptionevenement)) {
            $this->inscriptionevenements->add($inscriptionevenement);
            $inscriptionevenement->setEvenement($this);
        }
        return $this;
    }

    public function removeInscriptionevenement(Inscriptionevenement $inscriptionevenement): self
    {
        if ($this->inscriptionevenements->removeElement($inscriptionevenement)) {
            if ($inscriptionevenement->getEvenement() === $this) {
                $inscriptionevenement->setEvenement(null);
            }
        }
        return $this;
    }
}