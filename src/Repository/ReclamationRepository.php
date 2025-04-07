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

    /**
     * Find reclamations within a date range
     * 
     * @param \DateTime|null $startDate Start date for the range (inclusive)
     * @param \DateTime|null $endDate End date for the range (inclusive)
     * @return Reclamation[] Returns an array of Reclamation objects
     */
    public function findByDateRange(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->select('r.IdReclamation', 'r.description', 'r.typeR', 'r.date', 'r.statut', 'r.coach', 'r.adherent')
            ->orderBy('r.date', 'DESC');
        
        if ($startDate) {
            // Set time to beginning of day (00:00:00)
            $startDate->setTime(0, 0, 0);
            $queryBuilder
                ->andWhere('r.date >= :startDate')
                ->setParameter('startDate', $startDate);
        }
        
        if ($endDate) {
            // Set time to end of day (23:59:59)
            $endDate->setTime(23, 59, 59);
            $queryBuilder
                ->andWhere('r.date <= :endDate')
                ->setParameter('endDate', $endDate);
        }
        
        return $queryBuilder->getQuery()->getResult();
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
}
