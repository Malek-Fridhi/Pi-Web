<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert; 

use App\Repository\DepenseRepository;

#[ORM\Entity(repositoryClass: DepenseRepository::class)]
#[ORM\UniqueConstraint(
    name: 'unique_depense', 
    columns: ['typeDep', 'montantDep', 'datereceptionDep']
)]
#[ORM\Table(name: 'depense')]
class Depense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    //#[ORM\Column(type: 'integer')]
    #[ORM\Column(name: 'iddepense', type: 'integer')]
    private ?int $iddepense = null;

    public function getIddepense(): ?int
    {
        return $this->iddepense;
    }

    public function setIddepense(int $iddepense): self
    {
        $this->iddepense = $iddepense;
        return $this;
    }

    //#[ORM\Column(type: 'string', nullable: false)]
    #[ORM\Column(name: 'typeDep', type: 'string',nullable: false)]
    #[Assert\NotBlank(message:"Type est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le type doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le type ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Type(
        type: 'string',
        message: "Le type doit être une chaîne de caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s\-']+$/u",
        message: "Le type ne doit contenir que des lettres, espaces et tirets"
    )]

    private ?string $typeDep = null;

    public function getTypeDep(): ?string
    {
        return $this->typeDep;
    }

    public function setTypeDep(string $typeDep): self
    {
        $this->typeDep = $typeDep;
        return $this;
    }

    //#[ORM\Column(type: 'decimal', nullable: false)]
    #[ORM\Column(name: 'montantDep', type: 'decimal',nullable: false)]
    #[Assert\NotBlank(message: "Le montant est obligatoire")]
    #[Assert\Positive(message: "Le montant doit être positif")]
    #[Assert\Type(
        type: 'numeric',
        message: "Le montant doit être un nombre"
    )]

    private ?float $montantDep = null;

    public function getMontantDep(): ?float
    {
        return $this->montantDep;
    }

    public function setMontantDep(float $montantDep): self
    {
        $this->montantDep = $montantDep;
        return $this;
    }

    //#[ORM\Column(type: 'date', nullable: false)]
    #[ORM\Column(name: 'datereceptionDep', type: 'date',nullable: false)]
#[Assert\NotBlank(message: "La date est obligatoire")]
    #[Assert\LessThanOrEqual(
        value: "today",
        message: "La date ne peut pas être dans le futur"
    )]
    
    private ?\DateTimeInterface $datereceptionDep = null;

    public function getDatereceptionDep(): ?\DateTimeInterface
    {
        return $this->datereceptionDep;
    }

    public function setDatereceptionDep(\DateTimeInterface $datereceptionDep): self
    {
        $this->datereceptionDep = $datereceptionDep;
        return $this;
    }

    #[ORM\ManyToMany(targetEntity: Finance::class, inversedBy: 'depenses')]
#[ORM\JoinTable(name: 'rapportDepense',
    joinColumns: [new ORM\JoinColumn(name: 'iddepense', referencedColumnName: 'iddepense')],
    inverseJoinColumns: [new ORM\JoinColumn(name: 'idrapport', referencedColumnName: 'idfinance')]
)]
private Collection $finances;

public function __construct()
{
    $this->finances = new ArrayCollection();
}

public function getFinances(): Collection
{
    return $this->finances;
}


public function setFinance(?Finance $finance): self
{
    $this->finance = $finance;
    return $this;
}

/*public function addFinance(Finance $finance): self
{
    if (!$this->finances->contains($finance)) {
        // Vérifie qu'aucun rapport n'est déjà associé
        if ($this->finances->count() > 0) {
            throw new \RuntimeException('Une dépense ne peut appartenir qu\'à un seul rapport');
        }
        $this->finances[] = $finance;
        $finance->addDepense($this);
    }
    return $this;
}*/


    /*public function getFinances(): Collection
    {
        if (!$this->finances instanceof Collection) {
            $this->finances = new ArrayCollection();
        }
        return $this->finances;
    }*/

    /*public function addFinance(Finance $finance): self
    {
        if (!$this->getFinances()->contains($finance)) {
            $this->getFinances()->add($finance);
        }
        return $this;
    }*/

    /*public function removeFinance(Finance $finance): self
    {
        $this->getFinances()->removeElement($finance);
        return $this;
    }*/
    

}
