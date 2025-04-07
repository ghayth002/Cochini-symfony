<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\AdherentRepository;

#[ORM\Entity(repositoryClass: AdherentRepository::class)]
#[ORM\Table(name: 'adherent')]
class Adherent
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

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    // Relationship with User Entity
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'adherents')]
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

    // Poids Property (Weight)
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $Poids = null;

    public function getPoids(): ?float
    {
        return $this->Poids;
    }

    public function setPoids(?float $Poids): self
    {
        $this->Poids = $Poids;
        return $this;
    }

    // Taille Property (Height)
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $Taille = null;

    public function getTaille(): ?float
    {
        return $this->Taille;
    }

    public function setTaille(?float $Taille): self
    {
        $this->Taille = $Taille;
        return $this;
    }

    // Age Property
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $Age = null;

    public function getAge(): ?int
    {
        return $this->Age;
    }

    public function setAge(?int $Age): self
    {
        $this->Age = $Age;
        return $this;
    }

    // Genre Property (Gender)
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Genre = null;

    public function getGenre(): ?string
    {
        return $this->Genre;
    }

    public function setGenre(?string $Genre): self
    {
        $this->Genre = $Genre;
        return $this;
    }

    // Objectif Personnelle Property (Personal Goal)
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Objectif_personnelle = null;

    public function getObjectif_personnelle(): ?string
    {
        return $this->Objectif_personnelle;
    }

    public function setObjectif_personnelle(?string $Objectif_personnelle): self
    {
        $this->Objectif_personnelle = $Objectif_personnelle;
        return $this;
    }

    // Niveau Activites Property (Activity Level)
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Niveau_activites = null;

    public function getNiveau_activites(): ?string
    {
        return $this->Niveau_activites;
    }

    public function setNiveau_activites(?string $Niveau_activites): self
    {
        $this->Niveau_activites = $Niveau_activites;
        return $this;
    }

    // Relationship with PaiementPlanning (Payments)
    #[ORM\OneToMany(targetEntity: PaiementPlanning::class, mappedBy: 'adherent')]
    private Collection $paiementPlannings;

    /**
     * @return Collection<int, PaiementPlanning>
     */
    public function getPaiementPlannings(): Collection
    {
        if (!$this->paiementPlannings instanceof Collection) {
            $this->paiementPlannings = new ArrayCollection();
        }
        return $this->paiementPlannings;
    }

    public function addPaiementPlanning(PaiementPlanning $paiementPlanning): self
    {
        if (!$this->getPaiementPlannings()->contains($paiementPlanning)) {
            $this->getPaiementPlannings()->add($paiementPlanning);
        }
        return $this;
    }

    public function removePaiementPlanning(PaiementPlanning $paiementPlanning): self
    {
        $this->getPaiementPlannings()->removeElement($paiementPlanning);
        return $this;
    }

    // Relationship with Seance (Sessions)
    #[ORM\OneToMany(targetEntity: Seance::class, mappedBy: 'adherent')]
    private Collection $seances;

    public function __construct()
    {
        $this->paiementPlannings = new ArrayCollection();
        $this->seances = new ArrayCollection();
    }

    /**
     * @return Collection<int, Seance>
     */
    public function getSeances(): Collection
    {
        if (!$this->seances instanceof Collection) {
            $this->seances = new ArrayCollection();
        }
        return $this->seances;
    }

    public function addSeance(Seance $seance): self
    {
        if (!$this->getSeances()->contains($seance)) {
            $this->getSeances()->add($seance);
        }
        return $this;
    }

    public function removeSeance(Seance $seance): self
    {
        $this->getSeances()->removeElement($seance);
        return $this;
    }

    public function getObjectifPersonnelle(): ?string
    {
        return $this->Objectif_personnelle;
    }

    public function setObjectifPersonnelle(?string $Objectif_personnelle): static
    {
        $this->Objectif_personnelle = $Objectif_personnelle;

        return $this;
    }

    public function getNiveauActivites(): ?string
    {
        return $this->Niveau_activites;
    }

    public function setNiveauActivites(?string $Niveau_activites): static
    {
        $this->Niveau_activites = $Niveau_activites;

        return $this;
    }
}
