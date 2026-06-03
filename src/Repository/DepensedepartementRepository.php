<?php

namespace App\Repository;

use App\Entity\Depensedepartement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depensedepartement>
 *
 * @method Depensedepartement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depensedepartement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depensedepartement[]    findAll()
 * @method Depensedepartement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepensedepartementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depensedepartement::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Depensedepartement $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Depensedepartement $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Depensedepartement[] Returns an array of Depensedepartement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Depensedepartement
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

     /**
     * Recherche des dépenses par critères
     */
    public function rechercheDepenses(
        array $criteria = [],
        ?\DateTime $dateDebut = null,
        ?\DateTime $dateFin = null,
        ?int $limit = null
    ): array {
        try {
            $qb = $this->createQueryBuilder('d');
            
            if (!isset($criteria['eglise'])) {
                return [];
            }
            
            $qb->where("d.deletedAt IS NULL")
               ->andWhere("d.eglise = :eglise")
               ->setParameter('eglise', $criteria['eglise']);
            
            // Jointure pour charger les relations
            $qb->leftJoin('d.departement', 'dep')
               ->addSelect('dep');
            
            // Filtre par département
            if (!empty($criteria['departement'])) {
                $qb->andWhere("d.departement = :departement")
                   ->setParameter('departement', $criteria['departement']);
            }
            
            // Filtre par dates
            if ($dateDebut && $dateFin) {
                $dateFinClone = clone $dateFin;
                $dateFinClone->setTime(23, 59, 59);
                
                $qb->andWhere('d.datedepense BETWEEN :dateDebut AND :dateFin')
                   ->setParameter('dateDebut', $dateDebut)
                   ->setParameter('dateFin', $dateFinClone);
            } elseif ($dateDebut) {
                $qb->andWhere('d.datedepense >= :dateDebut')
                   ->setParameter('dateDebut', $dateDebut);
            } elseif ($dateFin) {
                $dateFinClone = clone $dateFin;
                $dateFinClone->setTime(23, 59, 59);
                $qb->andWhere('d.datedepense <= :dateFin')
                   ->setParameter('dateFin', $dateFinClone);
            }
            
            $qb->orderBy('d.datedepense', 'DESC');
            
            if ($limit) {
                $qb->setMaxResults($limit);
            }
            
            return $qb->getQuery()->getResult();
            
        } catch (Exception $exc) {
            error_log('Erreur recherche dépenses: ' . $exc->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère le total des dépenses par département
     */
    public function getTotalDepensesByDepartement(int $departementId, ?\DateTime $dateDebut = null, ?\DateTime $dateFin = null): int
    {
        try {
            $qb = $this->createQueryBuilder('d')
               ->select('SUM(d.montant) as total')
               ->where('d.departement = :departementId')
               ->setParameter('departementId', $departementId);
            
            if ($dateDebut && $dateFin) {
                $dateFinClone = clone $dateFin;
                $dateFinClone->setTime(23, 59, 59);
                $qb->andWhere('d.datedepense BETWEEN :dateDebut AND :dateFin')
                   ->setParameter('dateDebut', $dateDebut)
                   ->setParameter('dateFin', $dateFinClone);
            }
            
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (Exception $exc) {
            error_log('Erreur: ' . $exc->getMessage());
            return 0;
        }
    }
}
