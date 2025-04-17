<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    //    /**
    //     * @return Reclamation[] Returns an array of Reclamation objects
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

    //    public function findOneBySomeField($value): ?Reclamation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function countTotalReclamations(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // 2️⃣ عدد الشكاوى حسب الحالة (status)
    public function countByStatus(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.status, COUNT(r.id) as count')
            ->groupBy('r.status')
            ->getQuery()
            ->getResult();
    }

    // 3️⃣ عدد الشكاوى حسب الأشهر
    public function countByMonth(): array
    {
        return $this->createQueryBuilder('r')
            ->select("SUBSTRING(r.date, 1, 7) as month, COUNT(r.id) as count")
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchWithSorting(?string $keyword, ?string $status, string $sort, string $direction)
{
    $qb = $this->createQueryBuilder('r');

    if ($keyword) {
        $qb->andWhere('r.description LIKE :keyword OR r.status LIKE :keyword OR r.reponse LIKE :keyword')
           ->setParameter('keyword', '%' . $keyword . '%');
    }

    if ($status) {
        $qb->andWhere('r.status = :status')
           ->setParameter('status', $status);
    }

    if (in_array($sort, ['date', 'status'])) {
        $qb->orderBy('r.' . $sort, $direction);
    }

    return $qb->getQuery()->getResult();
}

}
