<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\PanierproduitRepository;

#[ORM\Entity(repositoryClass: PanierproduitRepository::class)]
#[ORM\Table(name: 'panierproduit')]
class Panierproduit
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

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'panierproduits')]
    #[ORM\JoinColumn(name: 'produitId', referencedColumnName: 'id')]
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

    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: 'panierproduits')]
    #[ORM\JoinColumn(name: 'panierId', referencedColumnName: 'id')]
    private ?Panier $panier = null;

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $quantite = null;

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date = null;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    #[ORM\Column(type: 'float', nullable: false)]
    private ?float $montant = null;

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $etat_paiement = null;

    public function getEtat_paiement(): ?string
    {
        return $this->etat_paiement;
    }

    public function setEtat_paiement(string $etat_paiement): self
    {
        $this->etat_paiement = $etat_paiement;
        return $this;
    }

    public function getEtatPaiement(): ?string
    {
        return $this->etat_paiement;
    }

    public function setEtatPaiement(string $etat_paiement): static
    {
        $this->etat_paiement = $etat_paiement;

        return $this;
    }

}
