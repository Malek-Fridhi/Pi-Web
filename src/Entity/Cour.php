<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\CourRepository;

#[ORM\Entity(repositoryClass: CourRepository::class)]
#[ORM\Table(name: 'cours')]
class Cour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message:"nom de cour est obligatoire")]
    #[Assert\Length(
        min: 3,
        minMessage: "Votre nom de cour doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message:"description de cour est obligatoire")]
    #[Assert\Length(
        min: 5,
        minMessage: "Votre description de cour doit contenir au moins {{ limit }} caractères."
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

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\NotBlank(message:"description de cour est obligatoire")]
    #[Assert\Type(type: "numeric", message: "la capacite doit être un nombre")]
    #[Assert\GreaterThan(value: 0, message: "la capacite doit être supérieur à 0")]
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

    #[ORM\OneToMany(targetEntity: Planning::class, mappedBy: 'cour')]
    private Collection $plannings;

    /**
     * @return Collection<int, Planning>
     */
    public function getPlannings(): Collection
    {
        if (!$this->plannings instanceof Collection) {
            $this->plannings = new ArrayCollection();
        }
        return $this->plannings;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->getPlannings()->contains($planning)) {
            $this->getPlannings()->add($planning);
        }
        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        $this->getPlannings()->removeElement($planning);
        return $this;
    }

    #[ORM\ManyToMany(targetEntity: Abonnement::class, inversedBy: 'cours')]
    #[ORM\JoinTable(
        name: 'CourAbonnement',
        joinColumns: [
            new ORM\JoinColumn(name: 'idCours', referencedColumnName: 'id')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'idAbonnement', referencedColumnName: 'id')
        ]
    )]
    private Collection $abonnements;

    public function __construct()
    {
        $this->plannings = new ArrayCollection();
        $this->abonnements = new ArrayCollection();
    }

    /**
     * @return Collection<int, Abonnement>
     */
    public function getAbonnements(): Collection
    {
        if (!$this->abonnements instanceof Collection) {
            $this->abonnements = new ArrayCollection();
        }
        return $this->abonnements;
    }

    public function addAbonnement(Abonnement $abonnement): self
    {
        if (!$this->getAbonnements()->contains($abonnement)) {
            $this->getAbonnements()->add($abonnement);
        }
        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): self
    {
        $this->getAbonnements()->removeElement($abonnement);
        return $this;
    }

}
