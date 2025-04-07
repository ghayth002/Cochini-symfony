<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\SeanceRepository;

#[ORM\Entity(repositoryClass: SeanceRepository::class)]
#[ORM\Table(name: 'seance')]
class Seance
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Titre = null;

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(?string $Titre): self
    {
        $this->Titre = $Titre;
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

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $Date = null;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(?\DateTimeInterface $Date): self
    {
        $this->Date = $Date;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Coach::class, inversedBy: 'seances')]
    #[ORM\JoinColumn(name: 'idCoach', referencedColumnName: 'id')]
    private ?Coach $coach = null;

    public function getCoach(): ?Coach
    {
        return $this->coach;
    }

    public function setCoach(?Coach $coach): self
    {
        $this->coach = $coach;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Adherent::class, inversedBy: 'seances')]
    #[ORM\JoinColumn(name: 'idAdherent', referencedColumnName: 'id')]
    private ?Adherent $adherent = null;

    public function getAdherent(): ?Adherent
    {
        return $this->adherent;
    }

    public function setAdherent(?Adherent $adherent): self
    {
        $this->adherent = $adherent;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Type = null;

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(?string $Type): self
    {
        $this->Type = $Type;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $LienVideo = null;

    public function getLienVideo(): ?string
    {
        return $this->LienVideo;
    }

    public function setLienVideo(?string $LienVideo): self
    {
        $this->LienVideo = $LienVideo;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Planning::class, inversedBy: 'seances')]
    #[ORM\JoinColumn(name: 'Planning_id', referencedColumnName: 'id')]
    private ?Planning $planning = null;

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function setPlanning(?Planning $planning): self
    {
        $this->planning = $planning;
        return $this;
    }

    #[ORM\Column(type: 'time', nullable: true)]
    private ?string $heureDebut = null;

    public function getHeureDebut(): ?string
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(?string $heureDebut): self
    {
        $this->heureDebut = $heureDebut;
        return $this;
    }

    #[ORM\Column(type: 'time', nullable: true)]
    private ?string $heureFin = null;

    public function getHeureFin(): ?string
    {
        return $this->heureFin;
    }

    public function setHeureFin(?string $heureFin): self
    {
        $this->heureFin = $heureFin;
        return $this;
    }

}
