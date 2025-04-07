<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\PaiementPlanningRepository;

#[ORM\Entity(repositoryClass: PaiementPlanningRepository::class)]
#[ORM\Table(name: 'paiement_planning')]
class PaiementPlanning
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_paiement = null;

    public function getId_paiement(): ?int
    {
        return $this->id_paiement;
    }

    public function setId_paiement(int $id_paiement): self
    {
        $this->id_paiement = $id_paiement;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Adherent::class, inversedBy: 'paiementPlannings')]
    #[ORM\JoinColumn(name: 'id_adherent', referencedColumnName: 'id')]
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

    #[ORM\ManyToOne(targetEntity: Planning::class, inversedBy: 'paiementPlannings')]
    #[ORM\JoinColumn(name: 'id_planning', referencedColumnName: 'id')]
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

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_paiement = null;

    public function getDate_paiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDate_paiement(\DateTimeInterface $date_paiement): self
    {
        $this->date_paiement = $date_paiement;
        return $this;
    }

    public function getIdPaiement(): ?int
    {
        return $this->id_paiement;
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

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(\DateTimeInterface $date_paiement): static
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

}
