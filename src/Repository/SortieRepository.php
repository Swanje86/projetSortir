<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }



    public function findSortieBySearchTerm(string $term)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.nom LIKE :term')
            ->setParameter('term', '%'.$term.'%');

        return $qb->getQuery()->getResult();
    }



    public function findSortieByDateRange( ?\DateTime $dateStartFilter,  ?\DateTime $dateEndFilter)
    {
        $qb = $this->createQueryBuilder('s');

        if ($dateStartFilter) {
            $qb->andWhere('s.dateSortie >= :dateStartFilter')
                ->setParameter('dateStartFilter', $dateStartFilter);
        }

        if ($dateEndFilter) {
            $qb->andWhere('s.dateSortie <= :dateEndFilter')
                ->setParameter('dateEndFilter', $dateEndFilter);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
