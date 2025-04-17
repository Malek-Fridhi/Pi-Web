<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\EquipementRepository;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
#[ORM\Table(name: 'equipements')]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_equipement = null;

    public function getId_equipement(): ?int
    {
        return $this->id_equipement;
    }

    public function setId_equipement(int $id_equipement): self
    {
        $this->id_equipement = $id_equipement;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'float', nullable: false)]
    private ?float $prix = null;

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $etat = null;

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $quantite = null;

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $image = null;

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

   // #[ORM\Column(type: 'string', nullable: false)]
    //private ?string $marque = null;
    #[ORM\Column(type: 'json', nullable: false)]
    private $marque;
    /*public function getMarque(): ?string
    {
        return $this->marque;
    }*/
    public function getMarque(): ?string
{
    // Convert the array to a JSON string if it's an array
    return is_array($this->marque) ? json_encode($this->marque) : $this->marque;
}

    public function setMarque(string $marque): self
    {
        $this->marque = $marque;
        return $this;
    }

    public function getIdEquipement(): ?int
    {
        return $this->id_equipement;
    }
    public function getReadableImageName(): string
    {
        return $this->nom . '.jpg'; // Ex: "creatine.jpg"
    }

}
