<?php

namespace App\Repository;

use App\Entity\Revenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Revenu>
 */
class RevenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Revenu::class);
    }

    public function findAvailableForReport(int $mois, int $annee)
{
    return $this->createQueryBuilder('r')
        ->leftJoin('r.finances', 'f')
        ->where('MONTH(r.datereceptionRevenu) = :mois')
        ->andWhere('YEAR(r.datereceptionRevenu) = :annee')
        ->andWhere('f.idfinance IS NULL')
        ->setParameter('mois', $mois)
        ->setParameter('annee', $annee)
        ->getQuery()
        ->getResult();
}

public function findUnassociatedByMonthAndYear(int $mois, int $annee)
{
    return $this->createQueryBuilder('r')
        ->leftJoin('App\Entity\RapportRevenu', 'rr', 'WITH', 'rr.revenu = r.idrevenu')
        ->where('rr.id IS NULL')
        ->andWhere('MONTH(r.datereceptionRevenu) = :mois')
        ->andWhere('YEAR(r.datereceptionRevenu) = :annee')
        ->setParameter('mois', $mois)
        ->setParameter('annee', $annee)
        ->getQuery()
        ->getResult();
}
    //    /**
    //     * @return Revenu[] Returns an array of Revenu objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Revenu
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
