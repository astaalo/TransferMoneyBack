<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *      normalizationContext={"groups"={"commission:read"}},
 *     attributes={
 *          "security"="is_granted('ROLE_AdminSystem') || is_granted('ROLE_AdminAgent')",
 *          "securityMessage"="Accès refusé !"
 *     },
 *      collectionOperations={
 *          "get"={"path"="admin/commissions"},
 * 
 *      "codeTrans"={
 *              "method"="GET",
 *              "path"="/transaction/{codeTrans}",
 *     },
 *     },
 *     itemOperations={
 *          "get"={"path"="/admin/commissions/{id}"},
 *     }
 * )
 * @ORM\Entity(repositoryClass=CommissionRepository::class)
 */
class Commission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"commission:read"})
     */
    private $partEtat;

    /**
     * @ORM\Column(type="integer")
     *  @Groups ({"commission:read"})
     */
    private $partSystem;

    /**
     * @ORM\Column(type="integer")
     *  @Groups ({"commission:read"})
     */
    private $partDepot;

    /**
     * @ORM\Column(type="integer")
     *  @Groups ({"commission:read"})
     */
    private $partRetrait;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartEtat(): ?int
    {
        return $this->partEtat;
    }

    public function setPartEtat(int $partEtat): self
    {
        $this->partEtat = $partEtat;

        return $this;
    }

    public function getPartSystem(): ?int
    {
        return $this->partSystem;
    }

    public function setPartSystem(int $partSystem): self
    {
        $this->partSystem = $partSystem;

        return $this;
    }

    public function getPartDepot(): ?int
    {
        return $this->partDepot;
    }

    public function setPartDepot(int $partDepot): self
    {
        $this->partDepot = $partDepot;

        return $this;
    }

    public function getPartRetrait(): ?int
    {
        return $this->partRetrait;
    }

    public function setPartRetrait(int $partRetrait): self
    {
        $this->partRetrait = $partRetrait;

        return $this;
    }
}
