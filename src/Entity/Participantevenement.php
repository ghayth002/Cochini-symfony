<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ParticipantevenementRepository;

#[ORM\Entity(repositoryClass: ParticipantevenementRepository::class)]
#[ORM\Table(name: 'participantevenement')]
class Participantevenement
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'participantevenements')]
    #[ORM\JoinColumn(name: 'userId', referencedColumnName: 'id')]
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

    #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: 'participantevenements')]
    #[ORM\JoinColumn(name: 'evenementId', referencedColumnName: 'id')]
    private ?Evenement $evenement = null;

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): self
    {
        $this->evenement = $evenement;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_inscription = null;

    public function getDate_inscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDate_inscription(?\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $etat_paiement = null;

    public function getEtat_paiement(): ?string
    {
        return $this->etat_paiement;
    }

    public function setEtat_paiement(?string $etat_paiement): self
    {
        $this->etat_paiement = $etat_paiement;
        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(?\DateTimeInterface $date_inscription): static
    {
        $this->date_inscription = $date_inscription;

        return $this;
    }

    public function getEtatPaiement(): ?string
    {
        return $this->etat_paiement;
    }

    public function setEtatPaiement(?string $etat_paiement): static
    {
        $this->etat_paiement = $etat_paiement;

        return $this;
    }

}
