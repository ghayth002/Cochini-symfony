<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\OffreproduitRepository;

#[ORM\Entity(repositoryClass: OffreproduitRepository::class)]
#[ORM\Table(name: 'offreproduit')]
class Offreproduit
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

    #[ORM\ManyToOne(targetEntity: Offre::class, inversedBy: 'offreproduits')]
    #[ORM\JoinColumn(name: 'offre_id', referencedColumnName: 'id')]
    private ?Offre $offre = null;

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): self
    {
        $this->offre = $offre;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'offreproduits')]
    #[ORM\JoinColumn(name: 'idProduit', referencedColumnName: 'id')]
    private ?Produit $produit = null;

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;
        return $this;
    }

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    private ?float $nouveauPrix = null;

    public function getNouveauPrix(): ?float
    {
        return $this->nouveauPrix;
    }

    public function setNouveauPrix(float $nouveauPrix): self
    {
        $this->nouveauPrix = $nouveauPrix;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $quantiteMax = null;

    public function getQuantiteMax(): ?int
    {
        return $this->quantiteMax;
    }

    public function setQuantiteMax(int $quantiteMax): self
    {
        $this->quantiteMax = $quantiteMax;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $quantiteVendue = null;

    public function getQuantiteVendue(): ?int
    {
        return $this->quantiteVendue;
    }

    public function setQuantiteVendue(int $quantiteVendue): self
    {
        $this->quantiteVendue = $quantiteVendue;
        return $this;
    }

}
