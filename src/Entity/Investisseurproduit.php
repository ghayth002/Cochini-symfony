<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\InvestisseurproduitRepository;

#[ORM\Entity(repositoryClass: InvestisseurproduitRepository::class)]
#[ORM\Table(name: 'investisseurproduit')]
class Investisseurproduit
{
    // Primary Key - ID
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'investisseurproduits')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Nom_entreprise = null;

    public function getNom_entreprise(): ?string
    {
        return $this->Nom_entreprise;
    }

    public function setNom_entreprise(?string $Nom_entreprise): self
    {
        $this->Nom_entreprise = $Nom_entreprise;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $Description = null;

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Adresse = null;

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(?string $Adresse): self
    {
        $this->Adresse = $Adresse;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Telephone = null;

    public function getTelephone(): ?string
    {
        return $this->Telephone;
    }

    public function setTelephone(?string $Telephone): self
    {
        $this->Telephone = $Telephone;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $Certificat_valide = null;

    public function isCertificat_valide(): ?bool
    {
        return $this->Certificat_valide;
    }

    public function setCertificat_valide(?bool $Certificat_valide): self
    {
        $this->Certificat_valide = $Certificat_valide;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'investisseurproduit')]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        if (!$this->produits instanceof Collection) {
            $this->produits = new ArrayCollection();
        }
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->getProduits()->contains($produit)) {
            $this->getProduits()->add($produit);
        }
        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        $this->getProduits()->removeElement($produit);
        return $this;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->Nom_entreprise;
    }

    public function setNomEntreprise(?string $Nom_entreprise): static
    {
        $this->Nom_entreprise = $Nom_entreprise;

        return $this;
    }

    public function isCertificatValide(): ?bool
    {
        return $this->Certificat_valide;
    }

    public function setCertificatValide(?bool $Certificat_valide): static
    {
        $this->Certificat_valide = $Certificat_valide;

        return $this;
    }

}
