<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"trans:read"}},
 *     denormalizationContext={"groups"={"trans:write"}},
 *     attributes={
 *          "security"="is_granted('ROLE_AdminSystem') || is_granted('ROLE_AdminAgent') || is_granted('ROLE_Utilisateur')",
 *          "securityMessage"="Accès refusé !"
 *     },
 *     collectionOperations={
 *          "get"={"path"="admin/transactions"},
 *          "post"={"path"="admin/transactions/depot",
 *          "name_route"="add_frais",
 *         },
 *          "codeTrans"={
 *              "method"="GET",
 *              "path"="/transaction/{codeTrans}",
 *     },
 *      "montant"={
 *              "method"="GET",
 *              "path"="/transaction/frais/{montant}",
 *     },
 *      
 * },
 *     itemOperations={
 *          "put"={"path"="user/transactions/{id}"},
 *          "get"={"path"="admin/transactions/parts/comptes/{id}"},
 *          "get"={"path"="admin/transactions/parts/agences/{id}"},
 *         
 *     }
 * )
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"trans:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"trans:write","client:read","client:write","retrait_red"})
     */
    private $montant;

    /**
     * @ORM\Column(type="date")
     * @Groups({"trans:write","retrait_red"})
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"trans:read","trans:write","retrait_red"})
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="string")
     * @Groups({"trans:write","retrait_red"})
     */
    private $codeTrans;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"trans:write","retrait_red"})
     */
    private $frais;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"retrait_red","trans:write"})
     */
    private $fraisDepot;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"retrait_red","trans:write"})
     */
    private $fraisRetrait;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"retrait_red","trans:write"})
     */
    private $fraisEtat;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"retrait_red","trans:write"})
     */
    private $fraisSystem;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="transaction",cascade = {"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"trans:write"})
     */
    private $compte;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateAnnulation;


    /**
     * @ORM\Column(type="string")
     * @Groups({"trans:write"})
     */
    private $etatTrans=false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions",cascade={"persist"})
     * @Groups({"trans:write","retrait_red"})
     */
    private $userDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @Groups({"trans:write","retrait_red"})
     */
    private $userRetrait;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transaction",cascade={"persist", "remove"})
     * @Groups({"trans:write","retrait_red"})
     */
    private $clientDepot;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactionRetrait", cascade={"persist"})
     * @Groups({"trans:write","retrait_red"})
     */
    private $clientRetrait;

    /**
     * @ORM\OneToMany(targetEntity=Compte::class, mappedBy="CompteRetrait")
     * @Groups({"retrait_red","trans:write"})
     */
    private $comptes;

    public function __construct()
    {
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getCodeTrans(): ?string
    {
        return $this->codeTrans;
    }

    public function setCodeTrans(string $codeTrans): self
    {
        $this->codeTrans = $codeTrans;

        return $this;
    }

    public function getFrais(): ?int
    {
        return $this->frais;
    }

    public function setFrais(int $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getFraisDepot(): ?int
    {
        return $this->fraisDepot;
    }

    public function setFraisDepot(int $fraisDepot): self
    {
        $this->fraisDepot = $fraisDepot;

        return $this;
    }

    public function getFraisRetrait(): ?int
    {
        return $this->fraisRetrait;
    }

    public function setFraisRetrait(int $fraisRetrait): self
    {
        $this->fraisRetrait = $fraisRetrait;

        return $this;
    }

    public function getFraisEtat(): ?int
    {
        return $this->fraisEtat;
    }

    public function setFraisEtat(int $fraisEtat): self
    {
        $this->fraisEtat = $fraisEtat;

        return $this;
    }

    public function getFraisSystem(): ?int
    {
        return $this->fraisSystem;
    }

    public function setFraisSystem(int $fraisSystem): self
    {
        $this->fraisSystem = $fraisSystem;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    public function getDateAnnulation(): ?\DateTimeInterface
    {
        return $this->dateAnnulation;
    }

    public function setDateAnnulation(?\DateTimeInterface $dateAnnulation): self
    {
        $this->dateAnnulation = $dateAnnulation;

        return $this;
    }

    public function getEtatTrans(): ?string
    {
        return $this->etatTrans;
    }

    public function setEtatTrans(string $etatTrans): self
    {
        $this->etatTrans = $etatTrans;

        return $this;
    }

    public function getUserDepot(): ?User
    {
        return $this->userDepot;
    }

    public function setUserDepot(?User $userDepot): self
    {
        $this->userDepot = $userDepot;

        return $this;
    }

    public function getUserRetrait(): ?User
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?User $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }

    public function getClientDepot(): ?Client
    {
        return $this->clientDepot;
    }

    public function setClientDepot(?Client $clientDepot): self
    {
        $this->clientDepot = $clientDepot;

        return $this;
    }

    public function getClientRetrait(): ?Client
    {
        return $this->clientRetrait;
    }

    public function setClientRetrait(?Client $clientRetrait): self
    {
        $this->clientRetrait = $clientRetrait;

        return $this;
    }

    /**
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setCompteRetrait($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getCompteRetrait() === $this) {
                $compte->setCompteRetrait(null);
            }
        }

        return $this;
    }

}
