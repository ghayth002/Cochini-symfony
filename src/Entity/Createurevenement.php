<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\CreateurevenementRepository;

#[ORM\Entity(repositoryClass: CreateurevenementRepository::class)]
#[ORM\Table(name: 'createurevenement')]
class Createurevenement
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'createurevenements')]
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Nom_organisation = null;

    public function getNom_organisation(): ?string
    {
        return $this->Nom_organisation;
    }

    public function setNom_organisation(?string $Nom_organisation): self
    {
        $this->Nom_organisation = $Nom_organisation;
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Adresse = null;

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(?string $Adresse): self
    {
        $this->Adresse = $Adresse;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Telephone = null;

    public function getTelephone(): ?string
    {
        return $this->Telephone;
    }

    public function setTelephone(?string $Telephone): self
    {
        $this->Telephone = $Telephone;
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

    public function getNomOrganisation(): ?string
    {
        return $this->Nom_organisation;
    }

    public function setNomOrganisation(?string $Nom_organisation): static
    {
        $this->Nom_organisation = $Nom_organisation;

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
