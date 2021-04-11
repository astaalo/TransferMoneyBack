<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CaissierRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CaissierRepository::class)
 * @ApiResource(
 *     normalizationContext={"groups"={"caissier:read"}},
 *     attributes={
 *          "security"="is_granted('ROLE_AdminSystem')",
 *          "securityMessage"="Accès refusé !"
 *     },
 *     collectionOperations={
 *          "get"={"path"="/admin/caissiers"},
 *     },
 *     itemOperations={
 *          "get"={"path"="/admin/caissiers/{id}"},
 *          "delete"={"path"="/admin/caissiers/{id}"},
 *     }
 * )
 */
class Caissier extends User
{

}
