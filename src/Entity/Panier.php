<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\PanierRepository;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
#[ORM\Table(name: 'panier')]
class Panier
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'paniers')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
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

    #[ORM\OneToMany(targetEntity: Panierproduit::class, mappedBy: 'panier')]
    private Collection $panierproduits;

    public function __construct()
    {
        $this->panierproduits = new ArrayCollection();
    }

    /**
     * @return Collection<int, Panierproduit>
     */
    public function getPanierproduits(): Collection
    {
        if (!$this->panierproduits instanceof Collection) {
            $this->panierproduits = new ArrayCollection();
        }
        return $this->panierproduits;
    }

    public function addPanierproduit(Panierproduit $panierproduit): self
    {
        if (!$this->getPanierproduits()->contains($panierproduit)) {
            $this->getPanierproduits()->add($panierproduit);
        }
        return $this;
    }

    public function removePanierproduit(Panierproduit $panierproduit): self
    {
        $this->getPanierproduits()->removeElement($panierproduit);
        return $this;
    }

}
