<?php

namespace App\Repository;

use App\Entity\Depense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depense>
 */
class DepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depense::class);
    }
    public function findAvailableForReport(int $mois, int $annee)
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.finances', 'f')
            ->where('MONTH(d.datereceptionDep) = :mois')
            ->andWhere('YEAR(d.datereceptionDep) = :annee')
            ->andWhere('f.idfinance IS NULL')
            ->setParameter('mois', $mois)
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getResult();
    }

    public function findUnassociatedByMonthAndYear(int $mois, int $annee)
{
    $qb = $this->createQueryBuilder('d')
        ->leftJoin('App\Entity\RapportDepense', 'rd', 'WITH', 'rd.depense = d.iddepense')
        ->where('rd.id IS NULL');

    // Filtrage par mois/année en PHP après récupération
    $allDepenses = $qb->getQuery()->getResult();

    return array_filter($allDepenses, function($depense) use ($mois, $annee) {
        return $depense->getDatereceptionDep()->format('m') == $mois && 
               $depense->getDatereceptionDep()->format('Y') == $annee;
    });
}
    //    /**
    //     * @return Depense[] Returns an array of Depense objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Depense
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
