<?php

namespace App\Repository;

use App\Entity\Inscriptionevenement;
use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inscriptionevenement>
 *
 * @method Inscriptionevenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscriptionevenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscriptionevenement[]    findAll()
 * @method Inscriptionevenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionevenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscriptionevenement::class);
    }

    /**
     * Compte le nombre d'inscriptions acceptées pour un événement donné
     */
    public function countAcceptedForEvent(Evenement $event): int
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.idevenement = :event')
            ->andWhere('i.statut = :status')
            ->setParameter('event', $event)
            ->setParameter('status', 'Approved')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve toutes les inscriptions pour un événement donné
     */
    public function findByEvent(Evenement $event): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.idevenement = :event')
            ->setParameter('event', $event)
            ->orderBy('i.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les inscriptions par statut
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.statut = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les inscriptions d'un utilisateur
     */
    public function findByUser($user): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.idUser = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    // Vous pouvez conserver ces méthodes générées par défaut ou les supprimer si inutiles
    /*
    public function findOneBySomeField($value): ?Inscriptionevenement
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
    */
}