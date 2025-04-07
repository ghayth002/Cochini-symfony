<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\CoachRepository;

#[ORM\Entity(repositoryClass: CoachRepository::class)]
#[ORM\Table(name: 'coach')]
class Coach
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

    // ManyToOne relationship with User
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'coachs')]
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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $Annee_experience = null;

    public function getAnnee_experience(): ?int
    {
        return $this->Annee_experience;
    }

    public function setAnnee_experience(?int $Annee_experience): self
    {
        $this->Annee_experience = $Annee_experience;
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Specialite = null;

    public function getSpecialite(): ?string
    {
        return $this->Specialite;
    }

    public function setSpecialite(?string $Specialite): self
    {
        $this->Specialite = $Specialite;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $Note = null;

    public function getNote(): ?int
    {
        return $this->Note;
    }

    public function setNote(?int $Note): self
    {
        $this->Note = $Note;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $CV = null;

    public function getCV(): ?string
    {
        return $this->CV;
    }

    public function setCV(string $CV): self
    {
        $this->CV = $CV;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Offrecoach::class, mappedBy: 'coach')]
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

    #[ORM\OneToMany(targetEntity: Planning::class, mappedBy: 'coach')]
    private Collection $plannings;

    /**
     * @return Collection<int, Planning>
     */
    public function getPlannings(): Collection
    {
        if (!$this->plannings instanceof Collection) {
            $this->plannings = new ArrayCollection();
        }
        return $this->plannings;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->getPlannings()->contains($planning)) {
            $this->getPlannings()->add($planning);
        }
        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        $this->getPlannings()->removeElement($planning);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Seance::class, mappedBy: 'coach')]
    private Collection $seances;

    public function __construct()
    {
        $this->offrecoachs = new ArrayCollection();
        $this->plannings = new ArrayCollection();
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

    public function getAnneeExperience(): ?int
    {
        return $this->Annee_experience;
    }

    public function setAnneeExperience(?int $Annee_experience): static
    {
        $this->Annee_experience = $Annee_experience;

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
