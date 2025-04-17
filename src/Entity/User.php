<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: " Obligatoir.")]
    #[Assert\Email(message: "Please provide a valid email address.")]
    private ?string $email = null;

    // On utilise type "json" pour stocker un tableau de rôles
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: "Password is required.")]
    #[Assert\Length(min: 8, minMessage: "Password must be at least {{ limit }} characters long.")]

    private ?string $password = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: "Username is required")]
    #[Assert\Length(min: 3, max: 20, minMessage: "Username must be at least {{ limit }} characters")]
    private ?string $username = null;

    #[ORM\Column(type: 'string')]
    private ?string $tel = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $salaire = null;

    #[ORM\OneToMany(targetEntity: Evenement::class, mappedBy: 'user')]
    private Collection $evenements;

    #[ORM\OneToMany(targetEntity: Abonnement::class, mappedBy: 'user')]
    private Collection $abonnements;

    #[ORM\OneToMany(targetEntity: Feedbackevenement::class, mappedBy: 'user')]
    private Collection $feedbackevenements;

    #[ORM\OneToMany(targetEntity: Finance::class, mappedBy: 'user')]
    private Collection $finances;

    #[ORM\OneToMany(targetEntity: Inscriptionevenement::class, mappedBy: 'user')]
    private Collection $inscriptionevenements;

    #[ORM\OneToMany(targetEntity: Objectif::class, mappedBy: 'user')]
    private Collection $objectifs;

    #[ORM\OneToMany(targetEntity: PlanNutritionnel::class, mappedBy: 'user')]
    private Collection $planNutritionnels;

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'user')]
    private Collection $reclamations;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'user')]
    private Collection $reservations;

    #[ORM\ManyToMany(targetEntity: Planning::class, inversedBy: 'users')]
    #[ORM\JoinTable(
        name: 'PlanningUser',
        joinColumns: [new ORM\JoinColumn(name: 'idUser', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'idPlan', referencedColumnName: 'id')]
    )]
    private Collection $plannings;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
        $this->abonnements = new ArrayCollection();
        $this->feedbackevenements = new ArrayCollection();
        $this->finances = new ArrayCollection();
        $this->inscriptionevenements = new ArrayCollection();
        $this->objectifs = new ArrayCollection();
        $this->planNutritionnels = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->plannings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Retourne les rôles de l'utilisateur.
     * On ajoute toujours ROLE_USER.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantie que l'utilisateur ait toujours ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Retourne l'identifiant de l'utilisateur pour le système de sécurité.
     * Ici, on retourne l'email.
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Méthode pour rester compatible avec les anciennes versions
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getSalaire(): ?string
    {
        return $this->salaire;
    }

    public function setSalaire(?string $salaire): self
    {
        $this->salaire = $salaire;
        return $this;
    }

    public function getPlannings(): Collection
    {
        return $this->plannings;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->plannings->contains($planning)) {
            $this->plannings->add($planning);
        }
        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        $this->plannings->removeElement($planning);
        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements->add($evenement);
            $evenement->setUser($this);
        }
        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            if ($evenement->getUser() === $this) {
                $evenement->setUser(null);
            }
        }
        return $this;
    }

    // Les getters et setters des autres collections (Abonnements, Feedbackevenements, Finances, Inscriptionevenements, Objectifs, PlanNutritionnels, Reclamations, Reservations)
    // restent inchangés car ils ne concernent pas la sécurité.

    public function eraseCredentials(): void
    {
        // Si vous stockez des informations sensibles temporaires, nettoyez-les ici.
    }
}
