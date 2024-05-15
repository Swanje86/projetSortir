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



// Cette méthode prend en paramètre un tableau de filtres et construit une requête qui applique tous ces filtres.
    public function findSortiesWithFilters($filters, $searchTerm)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.siteOrganisateur = :siteOrganisateur')
            ->setParameter('siteOrganisateur', $siteOrganisateur)
            ->getQuery()
            ->getResult();
    }

// TABLEAU --> Cette méthode retourne certains champs de toutes les sorties.
    public function findSomeFields()
    {
        return $this->createQueryBuilder('s')
            ->join('s.etat', 'e')
            ->join('s.siteOrganisateur','l')
            ->join('s.organisateur', 'p')

            ->getQuery()
            ->getResult();
    }


// FILTERS -->

// Cette méthode retourne toutes les sorties organisées par un siteOrganisateur.
    public function findSortieBySite($siteOrganisateur){
        return $this->createQueryBuilder('s')
            ->andWhere('s.siteOrganisateur = :siteOrganisateur')
            ->setParameter('siteOrganisateur', $siteOrganisateur)
            ->getQuery()
            ->getResult();
    }


    // Cette méthode retourne toutes les sorties dont le nom contient un terme de recherche spécifique.
    public function findSortieBySearchTerm(string $term)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.nomSortie LIKE :term')
            ->setParameter('term', '%'.$term.'%');

        return $qb->getQuery()->getResult();
    }

    // Cette méthode retourne toutes les sorties dont la date est comprise entre deux dates spécifiques.
    public function findSortieByDateRange( ?\DateTime $dateStartFilter,  ?\DateTime $dateEndFilter)
    {
        $qb = $this->createQueryBuilder('s');

        if ($dateStartFilter) {
            $dateStartFilter = $dateStartFilter ->format('Y-m-d');
            $qb->andWhere('s.dateHeureDebut >= :dateStartFilter')
                ->setParameter('dateStartFilter', $dateStartFilter);
        }

        if ($dateEndFilter) {
            $dateEndFilter = $dateEndFilter->format('Y-m-d');
            $qb->andWhere('s.dateHeureDebut <= :dateEndFilter')
                ->setParameter('dateEndFilter', $dateEndFilter);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }


// Cette méthode prend en paramètre un tableau de filtres et construit une requête qui applique tous ces filtres.
    public function findSortiesWithFilters($filters, )
    {
        $qb = $this->createQueryBuilder('s');

    // Si le filtre 'siteOrganisateur' est défini, on l'ajoute à la requête.
        if (!empty($filters->getSiteOrganisateur())) {
            $qb->andWhere('s.siteOrganisateur = :siteOrganisateur')
                ->setParameter('siteOrganisateur', $filters->getSiteOrganisateur());
        }


        // Si le filtre 'searchTerm' est défini, on l'ajoute à la requête.
        if (!empty($filters->getSearchTerm())) {
            $qb->andWhere('s.nomSortie LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $filters->getSearchTerm() . '%');
        }

        // Si le filtre 'dateStartFilter' est défini, on l'ajoute à la requête.
        if (!empty($filters->getDateStartFilter())) {
            $qb->andWhere('s.dateHeureDebut >= :dateStartFilter')
                ->setParameter('dateStartFilter', $filters->getDateStartFilter());
        }

        // Si le filtre 'dateEndFilter' est défini, on l'ajoute à la requête.
        if (!empty($filters->getDateEndFilter())) {
            $qb->andWhere('s.dateHeureDebut <= :dateEndFilter')
                ->setParameter('dateEndFilter', $filters->getDateEndFilter());
        }

        // On exécute la requête et on retourne les résultats.
        return $qb->getQuery()->getResult();
    }


}
