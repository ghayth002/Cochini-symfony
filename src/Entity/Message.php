<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MessageRepository;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'message')]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $Contenu = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $Date_envoi = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messagesEnvoyes')]
    #[ORM\JoinColumn(name: 'Utilisateur_expediteur', referencedColumnName: 'id')]
    private ?User $expediteur = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messagesRecus')]
    #[ORM\JoinColumn(name: 'Utilisateur_destinataire', referencedColumnName: 'id')]
    private ?User $destinataire = null;

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->Contenu;
    }

    public function setContenu(?string $Contenu): self
    {
        $this->Contenu = $Contenu;
        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->Date_envoi;
    }

    public function setDateEnvoi(?\DateTimeInterface $Date_envoi): self
    {
        $this->Date_envoi = $Date_envoi;
        return $this;
    }

    public function getExpediteur(): ?User
    {
        return $this->expediteur;
    }

    public function setExpediteur(?User $expediteur): self
    {
        $this->expediteur = $expediteur;
        return $this;
    }

    public function getDestinataire(): ?User
    {
        return $this->destinataire;
    }

    public function setDestinataire(?User $destinataire): self
    {
        $this->destinataire = $destinataire;
        return $this;
    }
}
