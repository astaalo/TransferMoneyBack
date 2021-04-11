<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 * @UniqueEntity(
 * fields={"numCompte"},
 * message="Le Numero du compte doit être unique"
 * )
 * @ApiResource(
 *     normalizationContext={"groups"={"compte:read"}},
 *     denormalizationContext={"groups"={"compte:write"}},
 *     attributes={
 *          "security"="is_granted('ROLE_AdminSystem')",
 *          "securityMessage"="Accès refusé !"
 *     },
 *     collectionOperations={
 *          "get"={"path"="admin/comptes"},
 *          "post"={"path"="admin/comptes"},
 *     },
 *     itemOperations={
 *          "get"={"path"="admin/comptes/{id}/users"},
 *          "get"={"path"="admin/comptes/{id}"},
 *          "delete"={"path"="admin/comptes/{id}"},
 *     }
 * )
 */
class Compte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"compte:write","trans:read","trans:write","agence:read","agence:write","depottrans:read","depottrans:write","retrait_red"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Le Numero du compte doit etre unique")
     * @Groups({"compte:read","compte:write","trans:read","trans:write","agence:read","agence:write","depottrans:read","depottrans:write"})
     */
    private $numCompte;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(700000)
     * @Assert\NotBlank(message="Le Solde est obligatoire")
     * @Groups({"compte:read","compte:write","trans:read","trans:write","agence:read","agence:write","depottrans:read","depottrans:write","retrait_red"})
     */
    private $solde;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived = 0;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="compte")
     */
    private $depots;

    /**
     * @ORM\ManyToOne(targetEntity=Transaction::class, inversedBy="comptes")
     * 
     */
    private $CompteRetrait;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="compteDepot")
     */
    private $transactionDepot;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="compteRetrait")
     */
    private $TransactionRetrait;


    public function __construct()
    {
        $this->depots = new ArrayCollection();
        $this->transactionDepot = new ArrayCollection();
        $this->TransactionRetrait = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCompte(): ?int
    {
        return $this->numCompte;
    }

    public function setNumCompte(int $numCompte): self
    {
        $this->numCompte = $numCompte;

        return $this;
    }

    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(int $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

        return $this;
    }

    public function getCompteRetrait(): ?Transaction
    {
        return $this->CompteRetrait;
    }

    public function setCompteRetrait(?Transaction $CompteRetrait): self
    {
        $this->CompteRetrait = $CompteRetrait;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionDepot(): Collection
    {
        return $this->transactionDepot;
    }

    public function addTransactionDepot(Transaction $transactionDepot): self
    {
        if (!$this->transactionDepot->contains($transactionDepot)) {
            $this->transactionDepot[] = $transactionDepot;
            $transactionDepot->setCompteDepot($this);
        }

        return $this;
    }

    public function removeTransactionDepot(Transaction $transactionDepot): self
    {
        if ($this->transactionDepot->removeElement($transactionDepot)) {
            // set the owning side to null (unless already changed)
            if ($transactionDepot->getCompteDepot() === $this) {
                $transactionDepot->setCompteDepot(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionRetrait(): Collection
    {
        return $this->TransactionRetrait;
    }

    public function addTransactionRetrait(Transaction $transactionRetrait): self
    {
        if (!$this->TransactionRetrait->contains($transactionRetrait)) {
            $this->TransactionRetrait[] = $transactionRetrait;
            $transactionRetrait->setCompteRetrait($this);
        }

        return $this;
    }

    public function removeTransactionRetrait(Transaction $transactionRetrait): self
    {
        if ($this->TransactionRetrait->removeElement($transactionRetrait)) {
            // set the owning side to null (unless already changed)
            if ($transactionRetrait->getCompteRetrait() === $this) {
                $transactionRetrait->setCompteRetrait(null);
            }
        }

        return $this;
    }
}
