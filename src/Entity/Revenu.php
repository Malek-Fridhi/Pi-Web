<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\RevenuRepository;

#[ORM\Entity(repositoryClass: RevenuRepository::class)]
#[ORM\Table(name: 'revenu')]
class Revenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    //#[ORM\Column(type: 'integer')]
    #[ORM\Column(name: 'idrevenu', type: 'integer')]

    private ?int $idrevenu = null;

    public function getIdrevenu(): ?int
    {
        return $this->idrevenu;
    }

    public function setIdrevenu(int $idrevenu): self
    {
        $this->idrevenu = $idrevenu;
        return $this;
    }

    //#[ORM\Column(type: 'string', nullable: false)]
    #[ORM\Column(name: 'sourceRevenu', type: 'string',nullable: false)]
    private ?string $sourceRevenu = null;

    public function getSourceRevenu(): ?string
    {
        return $this->sourceRevenu;
    }

    public function setSourceRevenu(string $sourceRevenu): self
    {
        $this->sourceRevenu = $sourceRevenu;
        return $this;
    }

    //#[ORM\Column(type: 'decimal', nullable: false)]
    #[ORM\Column(name: 'montantRevenu', type: 'decimal',nullable: false)]

    private ?float $montantRevenu = null;

    public function getMontantRevenu(): ?float
    {
        return $this->montantRevenu;
    }

    public function setMontantRevenu(float $montantRevenu): self
    {
        $this->montantRevenu = $montantRevenu;
        return $this;
    }

    //#[ORM\Column(type: 'date', nullable: false)]
    #[ORM\Column(name: 'datereceptionRevenu', type: 'date',nullable: false)]

    private ?\DateTimeInterface $datereceptionRevenu = null;

    public function getDatereceptionRevenu(): ?\DateTimeInterface
    {
        return $this->datereceptionRevenu;
    }

    public function setDatereceptionRevenu(\DateTimeInterface $datereceptionRevenu): self
    {
        $this->datereceptionRevenu = $datereceptionRevenu;
        return $this;
    }

 

    #[ORM\OneToMany(mappedBy: 'revenu', targetEntity: RapportRevenu::class)]
    private Collection $rapportRevenus;
    
    public function __construct()
    {
        $this->rapportRevenus = new ArrayCollection();
    }
    
    public function getRapportRevenus(): Collection
    {
        return $this->rapportRevenus;
    }


    

}
