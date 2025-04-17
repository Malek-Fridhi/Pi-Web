<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    //#[ORM\Column(type: 'string', nullable: true)]
    #[ORM\Column(name: 'imageUrl', type: 'string', nullable: true)]
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

    /**
     * @return Collection<int, Feedbackevenement>
     */
    public function getFeedbackevenements(): Collection
    {
        if (!$this->feedbackevenements instanceof Collection) {
            $this->feedbackevenements = new ArrayCollection();
        }
        return $this->feedbackevenements;
    }

    public function addFeedbackevenement(Feedbackevenement $feedbackevenement): self
    {
        if (!$this->getFeedbackevenements()->contains($feedbackevenement)) {
            $this->getFeedbackevenements()->add($feedbackevenement);
        }
        return $this;
    }

    public function removeFeedbackevenement(Feedbackevenement $feedbackevenement): self
    {
        $this->getFeedbackevenements()->removeElement($feedbackevenement);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Inscriptionevenement::class, mappedBy: 'evenement')]
    private Collection $inscriptionevenements;

    public function __construct()
    {
        $this->feedbackevenements = new ArrayCollection();
        $this->inscriptionevenements = new ArrayCollection();
    }

    /**
     * @return Collection<int, Inscriptionevenement>
     */
    public function getInscriptionevenements(): Collection
    {
        if (!$this->inscriptionevenements instanceof Collection) {
            $this->inscriptionevenements = new ArrayCollection();
        }
        return $this->inscriptionevenements;
    }

    public function addInscriptionevenement(Inscriptionevenement $inscriptionevenement): self
    {
        if (!$this->getInscriptionevenements()->contains($inscriptionevenement)) {
            $this->getInscriptionevenements()->add($inscriptionevenement);
        }
        return $this;
    }

    public function removeInscriptionevenement(Inscriptionevenement $inscriptionevenement): self
    {
        $this->getInscriptionevenements()->removeElement($inscriptionevenement);
        return $this;
    }

}
