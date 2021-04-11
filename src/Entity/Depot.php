<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\DepotRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *         normalizationContext={"groups"={"depottrans:read"}},
 *         denormalizationContext={"groups"={"depottrans:write"}},
 *      attributes={
 *          "security"="is_granted('ROLE_AdminSystem') || is_granted('ROLE_Caissier')",
 *          "securityMessage"="Accès refusé !",
 *     },
 *           collectionOperations={
 *          "get"={"path"="admin/caissiers/depot"},
 *          "post"={"path"="admin/caissiers/depot",
 *          "name_route"="depot_caissier",
 *         },
 *     },
 * )
 * @ORM\Entity(repositoryClass=DepotRepository::class)
 */
class Depot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"depottrans:read","depottrans:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"depottrans:read","depottrans:write"})
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"depottrans:read","depottrans:write"})
     */
    private $montantDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="depots",cascade={"persist"})
     * @Groups({"depottrans:read","depottrans:write"})
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="depots",cascade={"persist", "remove"})
     * @Groups({"depottrans:read","depottrans:write"})
     */
    private $compte;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMontantDepot(): ?int
    {
        return $this->montantDepot;
    }

    public function setMontantDepot(int $montantDepot): self
    {
        $this->montantDepot = $montantDepot;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

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
}
