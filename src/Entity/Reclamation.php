<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ReclamationRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
#[ORM\Table(name: 'reclamation')]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'IdReclamation', type: 'integer')]
    private ?int $IdReclamation = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $statut = false;

    public function getIdReclamation(): ?int
    {
        return $this->IdReclamation;
    }

    public function setIdReclamation(int $IdReclamation): self
    {
        $this->IdReclamation = $IdReclamation;
        return $this;
    }

    public function getStatut(): bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    #[Assert\Length(
        min: 10,
        max: 2000,
        minMessage: 'La description doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.'
    )]
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
    #[Assert\NotBlank(message: 'Le type de réclamation doit être spécifié.')]
    private ?string $typeR = null;

    public function getTypeR(): ?string
    {
        return $this->typeR;
    }

    public function setTypeR(?string $typeR): self
    {
        $this->typeR = $typeR;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ["persist"])]
    #[ORM\JoinColumn(name: 'Id_coach', referencedColumnName: 'id')]
    private ?User $coach = null;

    public function getCoach(): ?User
    {
        return $this->coach;
    }

    public function setCoach(?User $coach): self
    {
        $this->coach = $coach;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\NotNull(message: 'La date est requise.')]
    #[Assert\Type("\DateTimeInterface", message: 'La date doit être valide.')]
    private ?\DateTimeInterface $date = null;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ["persist"])]
    #[ORM\JoinColumn(name: 'Id_adherent', referencedColumnName: 'id')]
    #[Assert\NotNull(message: 'L\'adhérent doit être spécifié.')]
    private ?User $adherent = null;

    public function getAdherent(): ?User
    {
        return $this->adherent;
    }

    public function setAdherent(?User $adherent): self
    {
        $this->adherent = $adherent;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'reclamation')]
    private Collection $reponses;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        if (!$this->reponses instanceof Collection) {
            $this->reponses = new ArrayCollection();
        }
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->getReponses()->contains($reponse)) {
            $this->getReponses()->add($reponse);
        }
        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        $this->getReponses()->removeElement($reponse);
        return $this;
    }
}
