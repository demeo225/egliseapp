<?php

namespace App\Repository;

use App\Entity\Cotisationdepartement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cotisationdepartement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cotisationdepartement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cotisationdepartement[]    findAll()
 * @method Cotisationdepartement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotisationdepartementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotisationdepartement::class);
    }

    // /**
    //  * @return Cotisationdepartement[] Returns an array of Cotisationdepartement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    
        /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Cotisationdepartement $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /*
    public function findOneBySomeField($value): ?Cotisationdepartement
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
         public function findOneByCotisationdepartement($id): ?Cotisationdepartement {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

   public function rechercheCotisationsByDepartement(
    array $criteria = [],
    ?\DateTime $dateDebut = null,
    ?\DateTime $dateFin = null,
    ?int $limit = null
): array {
    try {
        $qb = $this->createQueryBuilder('c');
        
        if (!isset($criteria['eglise'])) {
            return [];
        }
        
        $qb->where("c.deletedAt IS NULL")
           ->andWhere("c.eglise = :eglise")
           ->setParameter('eglise', $criteria['eglise']);
        
        // Jointure pour charger les relations
        $qb->leftJoin('c.departement', 'd')
           ->addSelect('d');
        
        // Filtre par département
        if (!empty($criteria['departement'])) {
            $qb->andWhere("c.departement = :departement")
               ->setParameter('departement', $criteria['departement']);
        }
        
        // Filtre par dates (utilise createAt ou datecotiser selon votre besoin)
        if ($dateDebut && $dateFin) {
            $dateFinClone = clone $dateFin;
            $dateFinClone->setTime(23, 59, 59);
            
            $qb->andWhere('c.createAt BETWEEN :dateDebut AND :dateFin')
               ->setParameter('dateDebut', $dateDebut)
               ->setParameter('dateFin', $dateFinClone);
        } elseif ($dateDebut) {
            $qb->andWhere('c.createAt >= :dateDebut')
               ->setParameter('dateDebut', $dateDebut);
        } elseif ($dateFin) {
            $dateFinClone = clone $dateFin;
            $dateFinClone->setTime(23, 59, 59);
            $qb->andWhere('c.createAt <= :dateFin')
               ->setParameter('dateFin', $dateFinClone);
        }
        
        $qb->orderBy('c.createAt', 'DESC');
        
        if ($limit) {
            $qb->setMaxResults($limit);
        }
        
        return $qb->getQuery()->getResult();
        
    } catch (\Exception $exc) {
        error_log('Erreur recherche cotisations: ' . $exc->getMessage());
        return [];
    }
    }
}
