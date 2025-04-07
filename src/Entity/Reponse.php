<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\ReponseRepository;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
#[ORM\Table(name: 'reponse')]
class Reponse
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

    #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: 'reponses')]
    #[ORM\JoinColumn(name: 'Id_Reclamation', referencedColumnName: 'IdReclamation')]
    #[Assert\NotNull(message: 'La réclamation associée doit être spécifiée.')]
    private ?Reclamation $reclamation = null;

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): self
    {
        $this->reclamation = $reclamation;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\NotNull(message: 'La date de réponse est requise.')]
    #[Assert\Type("\DateTimeInterface", message: 'La date doit être valide.')]
    private ?\DateTimeInterface $Date_reponse = null;

    public function getDate_reponse(): ?\DateTimeInterface
    {
        return $this->Date_reponse;
    }

    public function setDate_reponse(?\DateTimeInterface $Date_reponse): self
    {
        $this->Date_reponse = $Date_reponse;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: 'Le contenu de la réponse ne peut pas être vide.')]
    #[Assert\Length(
        min: 5,
        max: 2000,
        minMessage: 'Le contenu doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le contenu ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $Contenu = null;

    public function getContenu(): ?string
    {
        return $this->Contenu;
    }

    public function setContenu(?string $Contenu): self
    {
        $this->Contenu = $Contenu;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le statut de la réponse doit être spécifié.')]
    #[Assert\Choice(
        choices: ['en attente', 'valider'],
        message: 'Le statut doit être soit "en attente", soit "valider".'
    )]
    private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->Date_reponse;
    }

    public function setDateReponse(?\DateTimeInterface $Date_reponse): static
    {
        $this->Date_reponse = $Date_reponse;

        return $this;
    }
}
