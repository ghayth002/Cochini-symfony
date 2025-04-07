<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\OffreRepository;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
#[ORM\Table(name: 'offre')]
class Offre
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

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $duree_validite = null;

    public function getDuree_validite(): ?\DateTimeInterface
    {
        return $this->duree_validite;
    }

    public function setDuree_validite(\DateTimeInterface $duree_validite): self
    {
        $this->duree_validite = $duree_validite;
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

    #[ORM\OneToMany(targetEntity: Offrecoach::class, mappedBy: 'offre')]
    private Collection $offrecoachs;

    /**
     * @return Collection<int, Offrecoach>
     */
    public function getOffrecoachs(): Collection
    {
        if (!$this->offrecoachs instanceof Collection) {
            $this->offrecoachs = new ArrayCollection();
        }
        return $this->offrecoachs;
    }

    public function addOffrecoach(Offrecoach $offrecoach): self
    {
        if (!$this->getOffrecoachs()->contains($offrecoach)) {
            $this->getOffrecoachs()->add($offrecoach);
        }
        return $this;
    }

    public function removeOffrecoach(Offrecoach $offrecoach): self
    {
        $this->getOffrecoachs()->removeElement($offrecoach);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Offreproduit::class, mappedBy: 'offre')]
    private Collection $offreproduits;

    public function __construct()
    {
        $this->offrecoachs = new ArrayCollection();
        $this->offreproduits = new ArrayCollection();
    }

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

    public function getDureeValidite(): ?\DateTimeInterface
    {
        return $this->duree_validite;
    }

    public function setDureeValidite(\DateTimeInterface $duree_validite): static
    {
        $this->duree_validite = $duree_validite;

        return $this;
    }

}
