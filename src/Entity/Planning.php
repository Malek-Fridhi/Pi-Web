<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\PlanningRepository;

#[ORM\Entity(repositoryClass: PlanningRepository::class)]
#[ORM\Table(name: 'Planning')]
class Planning
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

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotBlank(message: "La durée est obligatoire")]
    #[Assert\Type(type: 'integer', message: "La durée doit être un nombre entier")]
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

    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\NotBlank(message: "La date est obligatoire")]
    #[Assert\Type(type: '\DateTimeInterface', message: "La date doit être valide")]
    #[Assert\GreaterThanOrEqual("today", message:"La date de début ne peut pas être dans le passé")]
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

    #[ORM\OneToOne(targetEntity: Cour::class)]
    #[ORM\JoinColumn(name: 'idCours', referencedColumnName: 'id')]
    #[Assert\NotBlank(message: "Le cours est obligatoire")]
    private ?Cour $cour = null;

    public function getCour(): ?Cour
    {
        return $this->cour;
    }

    public function setCour(?Cour $cour): self
    {
        $this->cour = $cour;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'plannings')]
    #[ORM\JoinColumn(name: 'idCoach', referencedColumnName: 'id')]
    #[Assert\NotBlank(message: "L'utilisateur est obligatoire")]
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

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'plannings')]
    #[ORM\JoinTable(
        name: 'PlanningUser',
        joinColumns: [
            new ORM\JoinColumn(name: 'idPlan', referencedColumnName: 'id')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'idUser', referencedColumnName: 'id')
        ]
    )]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);
        return $this;
    }
}
