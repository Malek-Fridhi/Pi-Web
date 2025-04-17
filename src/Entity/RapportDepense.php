<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'rapportDepense')]
class RapportDepense
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Depense::class)]
    #[ORM\JoinColumn(name: 'idDepense', referencedColumnName: 'iddepense')]
    private Depense $depense;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Finance::class)]
    #[ORM\JoinColumn(name: 'idRapport', referencedColumnName: 'idfinance')]
    private Finance $finance;

    public function getDepense(): ?Depense
    {
        return $this->depense;
    }

    public function setDepense(?Depense $depense): self
    {
        $this->depense = $depense;
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