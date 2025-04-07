<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ProduitRepository;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: 'produit')]
class Produit
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

    #[ORM\ManyToOne(targetEntity: Investisseurproduit::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(name: 'idInvestisseur', referencedColumnName: 'id')]
    private ?Investisseurproduit $investisseurproduit = null;

    public function getInvestisseurproduit(): ?Investisseurproduit
    {
        return $this->investisseurproduit;
    }

    public function setInvestisseurproduit(?Investisseurproduit $investisseurproduit): self
    {
        $this->investisseurproduit = $investisseurproduit;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $image = null;

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $etat = null;

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(name: 'categorieId', referencedColumnName: 'id')]
    private ?Categorie $categorie = null;

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;
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

    #[ORM\OneToMany(targetEntity: Offreproduit::class, mappedBy: 'produit')]
    private Collection $offreproduits;

    /**
     * @return Collection<int, Offreproduit>
     */
    public function getOffreproduits(): Collection
    {
        if (!$this->offreproduits instanceof Collection) {
            $this->offreproduits = new ArrayCollection();
        }
        return $this->offreproduits;
    }

    public function addOffreproduit(Offreproduit $offreproduit): self
    {
        if (!$this->getOffreproduits()->contains($offreproduit)) {
            $this->getOffreproduits()->add($offreproduit);
        }
        return $this;
    }

    public function removeOffreproduit(Offreproduit $offreproduit): self
    {
        $this->getOffreproduits()->removeElement($offreproduit);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Panierproduit::class, mappedBy: 'produit')]
    private Collection $panierproduits;

    public function __construct()
    {
        $this->offreproduits = new ArrayCollection();
        $this->panierproduits = new ArrayCollection();
    }

    /**
     * @return Collection<int, Panierproduit>
     */
    public function getPanierproduits(): Collection
    {
        if (!$this->panierproduits instanceof Collection) {
            $this->panierproduits = new ArrayCollection();
        }
        return $this->panierproduits;
    }

    public function addPanierproduit(Panierproduit $panierproduit): self
    {
        if (!$this->getPanierproduits()->contains($panierproduit)) {
            $this->getPanierproduits()->add($panierproduit);
        }
        return $this;
    }

    public function removePanierproduit(Panierproduit $panierproduit): self
    {
        $this->getPanierproduits()->removeElement($panierproduit);
        return $this;
    }

}
