<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\FinanceRepository;

#[ORM\Entity(repositoryClass: FinanceRepository::class)]
#[ORM\Table(name: 'finance')]
#[ORM\UniqueConstraint(
    name: 'unique_month_year_user', 
    columns: ['moisFin', 'anneeFin']
)]
class Finance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $idfinance = null;

    public function getIdfinance(): ?int
    {
        return $this->idfinance;
    }

    public function setIdfinance(int $idfinance): self
    {
        $this->idfinance = $idfinance;
        return $this;
    }

    //#[ORM\Column(type: 'integer', nullable: true)]
    #[ORM\Column(name: 'moisFin', type: 'integer',nullable: false)]
    #[Assert\NotBlank(message: "Le mois est obligatoire")]
    #[Assert\Range(
        min: 1,
        max: 12,
        notInRangeMessage: "Le mois doit être entre {{ min }} et {{ max }}"
    )]

    private ?int $moisFin = null;

    public function getMoisFin(): ?int
    {
        return $this->moisFin;
    }

    public function setMoisFin(?int $moisFin): self
    {
        $this->moisFin = $moisFin;
        return $this;
    }

    //#[ORM\Column(type: 'integer', nullable: true)]
    #[ORM\Column(name: 'anneeFin', type: 'integer',nullable: false)]
    #[Assert\NotBlank(message: "L'année est obligatoire")]
    

    private ?int $anneeFin = null;

    public function getAnneeFin(): ?int
    {
        return $this->anneeFin;
    }

    public function setAnneeFin(?int $anneeFin): self
    {
        $this->anneeFin = $anneeFin;
        return $this;
    }

    //#[ORM\Column(type: 'decimal', nullable: true)]
    #[ORM\Column(name: 'totalDepenses', type: 'decimal',nullable: false)]

    private ?float $totalDepenses = null;

    public function getTotalDepenses(): ?float
    {
        return $this->totalDepenses;
    }

    public function setTotalDepenses(?float $totalDepenses): self
    {
        $this->totalDepenses = $totalDepenses;
        return $this;
    }

    //#[ORM\Column(type: 'decimal', nullable: true)]
    #[ORM\Column(name: 'totalRevenus', type: 'decimal',nullable: false)]

    private ?float $totalRevenus = null;

    public function getTotalRevenus(): ?float
    {
        return $this->totalRevenus;
    }

    public function setTotalRevenus(?float $totalRevenus): self
    {
        $this->totalRevenus = $totalRevenus;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $profit = null;

    public function getProfit(): ?float
    {
        return $this->profit;
    }

    public function setProfit(?float $profit): self
    {
        $this->profit = $profit;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'finances')]
    #[ORM\JoinColumn(name: 'idcomp', referencedColumnName: 'id')]
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

    
    #[ORM\OneToMany(mappedBy: 'finance', targetEntity: RapportDepense::class, cascade: ['persist', 'remove'])]
    private Collection $rapportDepenses;
    
    #[ORM\OneToMany(mappedBy: 'finance', targetEntity: RapportRevenu::class, cascade: ['persist', 'remove'])]
    private Collection $rapportRevenus;
    


    public function __construct()
    {
        $this->rapportDepenses = new ArrayCollection();
        $this->rapportRevenus = new ArrayCollection();
    }
    /**
     * @return Collection<int, RapportDepense>
     */
    public function getDepenses(): Collection
    {
        return $this->rapportDepenses;
    }

    public function addDepense(RapportDepense $rapportDepense): self
    {
        if (!$this->rapportDepenses->contains($rapportDepense)) {
            $this->rapportDepenses[] = $rapportDepense;
           
        }
        return $this;
    }

    public function removeDepense(RapportDepense $rapportDepense): self
    {
        $this->rapportDepenses->removeElement($rapportDepense) ;
        
        return $this;
    }

    
    /**
     * @return Collection<int, RapportRevenu>
     */
    public function getRevenus(): Collection
    {
        return $this->rapportRevenus;
    }

    public function addRevenu(RapportRevenu $rapportRevenu): self
    {
        if (!$this->rapportRevenus->contains($rapportRevenu)) {
            $this->rapportRevenus[] = $rapportRevenu;
           
        }
        return $this;
    }

    public function removeRevenu(RapportRevenu $rapportRevenu): self
    {
        $this->rapportRevenus->removeElement($rapportRevenu) ;
        
        return $this;
    }

}
