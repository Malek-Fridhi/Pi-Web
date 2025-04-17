<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Entity\Revenu; 
use App\Entity\Finance;

#[ORM\Entity]
#[ORM\Table(name: 'rapportRevenu')]
class RapportRevenu
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Revenu::class)]
    #[ORM\JoinColumn(name: 'idRevenu', referencedColumnName: 'idrevenu')]
    private Revenu $revenu;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Finance::class,inversedBy: 'rapportRevenus')]
    #[ORM\JoinColumn(name: 'idRapport', referencedColumnName: 'idfinance')]
    private Finance $finance;

    public function getRevenu(): ?Revenu
    {
        return $this->revenu;
    }

    public function setRevenu(?Revenu $revenu): self
    {
        $this->revenu = $revenu;
        return $this;
    }
    public function getFinance(): ?Finance
    {
        return $this->finance;
    }

    public function setFinance(?Finance $finance): self
    {
        $this->finance = $finance;
        return $this;
    }
}