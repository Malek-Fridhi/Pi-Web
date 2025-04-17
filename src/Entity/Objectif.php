<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ObjectifRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ObjectifRepository::class)]
#[ORM\Table(name: 'objectif')]
class Objectif
{
    public const STATUS_EN_COURS = 'enCours';
    public const STATUS_ATTEINT = 'atteint';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idObjectif', type: 'integer')]
    private ?int $idObjectif = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'objectifs')]
    #[ORM\JoinColumn(name: 'userId', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide")]
    #[Assert\Length(
        min: 5,
        minMessage: "La description doit faire au moins {{ limit }} caractères"
    )]
    private string $description;

    #[ORM\Column(name: 'poidsActuel', type: 'float', nullable: false)]
    #[Assert\NotBlank(message: "Le poids actuel ne peut pas être vide")]
    #[Assert\Type(
        type: 'float',
        message: "Le poids actuel doit être un nombre valide"
    )]
    #[Assert\Positive(message: "Le poids actuel doit être un nombre positif")]
    private float $poidsActuel;

    #[ORM\Column(name: 'poidsCible', type: 'float', nullable: false)]
    #[Assert\NotBlank(message: "Le poids cible ne peut pas être vide")]
    #[Assert\Type(
        type: 'float',
        message: "Le poids cible doit être un nombre valide"
    )]
    #[Assert\Positive(message: "Le poids cible doit être un nombre positif")]
    private float $poidsCible;

    #[ORM\Column(type: 'string', length: 20, nullable: false, columnDefinition: "ENUM('enCours', 'atteint')")]
    private string $status = self::STATUS_EN_COURS;

    #[ORM\OneToMany(mappedBy: 'objectif', targetEntity: PlanNutritionnel::class, cascade: ['persist', 'remove'])]
    private Collection $planNutritionnels;

    public function __construct()
    {
        $this->planNutritionnels = new ArrayCollection();
    }

    public function getIdObjectif(): ?int
    {
        return $this->idObjectif;
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPoidsActuel(): float
    {
        return $this->poidsActuel;
    }

    public function setPoidsActuel(float $poidsActuel): self
    {
        $this->poidsActuel = $poidsActuel;
        return $this;
    }

    public function getPoidsCible(): float
    {
        return $this->poidsCible;
    }

    public function setPoidsCible(float $poidsCible): self
    {
        $this->poidsCible = $poidsCible;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, [self::STATUS_EN_COURS, self::STATUS_ATTEINT])) {
            throw new \InvalidArgumentException("Statut invalide");
        }
        $this->status = $status;
        return $this;
    }

    public function getPlanNutritionnels(): Collection
    {
        return $this->planNutritionnels;
    }

    public function addPlanNutritionnel(PlanNutritionnel $planNutritionnel): self
    {
        if (!$this->planNutritionnels->contains($planNutritionnel)) {
            $this->planNutritionnels[] = $planNutritionnel;
            $planNutritionnel->setObjectif($this);
        }
        return $this;
    }

    public function removePlanNutritionnel(PlanNutritionnel $planNutritionnel): self
    {
        if ($this->planNutritionnels->removeElement($planNutritionnel)) {
            if ($planNutritionnel->getObjectif() === $this) {
                $planNutritionnel->setObjectif(null);
            }
        }
        return $this;
    }
}