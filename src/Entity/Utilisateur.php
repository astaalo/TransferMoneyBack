<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"utilisateur:read"}},
 *     denormalizationContext={"groups"={"utilisateur:write"}},
 *     attributes={
 *          "security"="is_granted('ROLE_AdminAgence')",
 *          "securityMessage"="Accès refusé !"
 *     },
 *     collectionOperations={
 *          "get"={"path"="admin/utilisateurs"},
 *     },
 *     itemOperations={
 *          "get"={"path"="admin/utilisateurs/{id}"},
 *          "get"={"path"="admin/utilisateurs/{id}/users"},
 *          "delete"={"path"="admin/utilisateurs/{id}"},
 *     }
 * )
 */
class Utilisateur extends User
{

}
