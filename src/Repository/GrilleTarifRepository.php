<?php

namespace App\Repository;

use App\Entity\GrilleTarif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GrilleTarif|null find($id, $lockMode = null, $lockVersion = null)
 * @method GrilleTarif|null findOneBy(array $criteria, array $orderBy = null)
 * @method GrilleTarif[]    findAll()
 * @method GrilleTarif[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrilleTarifRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrilleTarif::class);
    }

    // /**
    //  * @return GrilleTarif[] Returns an array of GrilleTarif objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GrilleTarif
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
