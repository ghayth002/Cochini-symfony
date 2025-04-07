<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User
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
    private ?string $Nom = null;

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(?string $Nom): self
    {
        $this->Nom = $Nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Prenom = null;

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(?string $Prenom): self
    {
        $this->Prenom = $Prenom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Image = null;

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(?string $Image): self
    {
        $this->Image = $Image;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Email = null;

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(?string $Email): self
    {
        $this->Email = $Email;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $MDP = null;

    public function getMDP(): ?string
    {
        return $this->MDP;
    }

    public function setMDP(?string $MDP): self
    {
        $this->MDP = $MDP;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Adherent::class, mappedBy: 'user')]
    private Collection $adherents;

    public function getAdherents(): Collection
    {
        if (!$this->adherents instanceof Collection) {
            $this->adherents = new ArrayCollection();
        }
        return $this->adherents;
    }

    public function addAdherent(Adherent $adherent): self
    {
        if (!$this->getAdherents()->contains($adherent)) {
            $this->getAdherents()->add($adherent);
        }
        return $this;
    }

    public function removeAdherent(Adherent $adherent): self
    {
        $this->getAdherents()->removeElement($adherent);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Admin::class, mappedBy: 'user')]
    private Collection $admins;

    public function getAdmins(): Collection
    {
        if (!$this->admins instanceof Collection) {
            $this->admins = new ArrayCollection();
        }
        return $this->admins;
    }

    public function addAdmin(Admin $admin): self
    {
        if (!$this->getAdmins()->contains($admin)) {
            $this->getAdmins()->add($admin);
        }
        return $this;
    }

    public function removeAdmin(Admin $admin): self
    {
        $this->getAdmins()->removeElement($admin);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Coach::class, mappedBy: 'user')]
    private Collection $coachs;

    public function getCoachs(): Collection
    {
        if (!$this->coachs instanceof Collection) {
            $this->coachs = new ArrayCollection();
        }
        return $this->coachs;
    }

    public function addCoach(Coach $coach): self
    {
        if (!$this->getCoachs()->contains($coach)) {
            $this->getCoachs()->add($coach);
        }
        return $this;
    }

    public function removeCoach(Coach $coach): self
    {
        $this->getCoachs()->removeElement($coach);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Createurevenement::class, mappedBy: 'user')]
    private Collection $createurevenements;

    public function getCreateurevenements(): Collection
    {
        if (!$this->createurevenements instanceof Collection) {
            $this->createurevenements = new ArrayCollection();
        }
        return $this->createurevenements;
    }

    public function addCreateurevenement(Createurevenement $createurevenement): self
    {
        if (!$this->getCreateurevenements()->contains($createurevenement)) {
            $this->getCreateurevenements()->add($createurevenement);
        }
        return $this;
    }

    public function removeCreateurevenement(Createurevenement $createurevenement): self
    {
        $this->getCreateurevenements()->removeElement($createurevenement);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Investisseurproduit::class, mappedBy: 'user')]
    private Collection $investisseurproduits;

    public function getInvestisseurproduits(): Collection
    {
        if (!$this->investisseurproduits instanceof Collection) {
            $this->investisseurproduits = new ArrayCollection();
        }
        return $this->investisseurproduits;
    }

    public function addInvestisseurproduit(Investisseurproduit $investisseurproduit): self
    {
        if (!$this->getInvestisseurproduits()->contains($investisseurproduit)) {
            $this->getInvestisseurproduits()->add($investisseurproduit);
        }
        return $this;
    }

    public function removeInvestisseurproduit(Investisseurproduit $investisseurproduit): self
    {
        $this->getInvestisseurproduits()->removeElement($investisseurproduit);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'user')]
    private Collection $messages;

    public function getMessages(): Collection
    {
        if (!$this->messages instanceof Collection) {
            $this->messages = new ArrayCollection();
        }
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->getMessages()->contains($message)) {
            $this->getMessages()->add($message);
        }
        return $this;
    }

    public function removeMessage(Message $message): self
    {
        $this->getMessages()->removeElement($message);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Panier::class, mappedBy: 'user')]
    private Collection $paniers;

    public function getPaniers(): Collection
    {
        if (!$this->paniers instanceof Collection) {
            $this->paniers = new ArrayCollection();
        }
        return $this->paniers;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->getPaniers()->contains($panier)) {
            $this->getPaniers()->add($panier);
        }
        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        $this->getPaniers()->removeElement($panier);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Participantevenement::class, mappedBy: 'user')]
    private Collection $participantevenements;

    public function getParticipantevenements(): Collection
    {
        if (!$this->participantevenements instanceof Collection) {
            $this->participantevenements = new ArrayCollection();
        }
        return $this->participantevenements;
    }

    public function addParticipantevenement(Participantevenement $participantevenement): self
    {
        if (!$this->getParticipantevenements()->contains($participantevenement)) {
            $this->getParticipantevenements()->add($participantevenement);
        }
        return $this;
    }

    public function removeParticipantevenement(Participantevenement $participantevenement): self
    {
        $this->getParticipantevenements()->removeElement($participantevenement);
        return $this;
    }

    #[ORM\ManyToMany(targetEntity: Evenement::class, inversedBy: 'users')]
    #[ORM\JoinTable(
        name: 'participation',
        joinColumns: [
            new ORM\JoinColumn(name: 'userId', referencedColumnName: 'id')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'evenementId', referencedColumnName: 'id')
        ]
    )]
    private Collection $evenements;

    public function __construct()
    {
        $this->adherents = new ArrayCollection();
        $this->admins = new ArrayCollection();
        $this->coachs = new ArrayCollection();
        $this->createurevenements = new ArrayCollection();
        $this->investisseurproduits = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->paniers = new ArrayCollection();
        $this->participantevenements = new ArrayCollection();
        $this->evenements = new ArrayCollection();
    }

    public function getEvenements(): Collection
    {
        if (!$this->evenements instanceof Collection) {
            $this->evenements = new ArrayCollection();
        }
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->getEvenements()->contains($evenement)) {
            $this->getEvenements()->add($evenement);
        }
        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        $this->getEvenements()->removeElement($evenement);
        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s %s (ID: %d)', $this->Nom ?? 'Unknown', $this->Prenom ?? 'User', $this->id ?? 0);
    }

}
