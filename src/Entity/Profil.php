<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProfilRepository::class)
 * @ApiResource(
 *    routePrefix = "/admin",
 *  normalizationContext={"groups"={"profil:read"}},
 *  attributes={
 *       "security"="is_granted('ROLE_AdminSystem')",
 *       "security_message"="Vous n'avez pas access à cette Ressource"
 * },
 *     collectionOperations={
 *     "get","post",
 *      "get_role_admin"={
 *      "method"="GET",
 *      "path"="/profils" ,
 *      },
 *      "get_role_admin"={
 *      "method"="POST",
 *      "path"="/profils" ,
 *      },
 *  },
 * itemOperations={
 *      "get_role_admin"={
 *      "method"="GET",
 *      "path"="/profils/{id}" ,
 *      },
 *      "get_admin_put"={
 *      "method"="PUT",
 *      "path"="/profils/{id}" ,
 *      },
 *  }
 * )
 */
class Profil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read", "compte:read","profil:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "compte:read","profil:read"})
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="profil")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setProfil($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getProfil() === $this) {
                $user->setProfil(null);
            }
        }

        return $this;
    }
}
