<?php

namespace App\Repository;

use App\Entity\Cotiserdepartement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Exception;

/**
 * @method Cotiserdepartement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cotiserdepartement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cotiserdepartement[]    findAll()
 * @method Cotiserdepartement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotiserdepartementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotiserdepartement::class);
    }

    // /**
    //  * @return Cotiserdepartement[] Returns an array of Cotiserdepartement objects
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

    /*
    public function findOneBySomeField($value): ?Cotiserdepartement
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
          public function findOneByCotiserdepartement($id): ?Cotiserdepartement {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

     /**
     * Récupère les cotisations versées par département
     */
    public function getCotisationsByDepartement(int $departementId, ?\DateTime $dateDebut = null, ?\DateTime $dateFin = null): array
    {
        try {
            $qb = $this->createQueryBuilder('c')
               ->where('c.departement = :departementId')
               ->setParameter('departementId', $departementId)
               ->andWhere('c.deletedAt IS NULL');
            
            if ($dateDebut && $dateFin) {
                $dateFinClone = clone $dateFin;
                $dateFinClone->setTime(23, 59, 59);
                $qb->andWhere('c.datecotiser BETWEEN :dateDebut AND :dateFin')
                   ->setParameter('dateDebut', $dateDebut)
                   ->setParameter('dateFin', $dateFinClone);
            }
            
            $qb->orderBy('c.datecotiser', 'DESC');
            
            return $qb->getQuery()->getResult();
        } catch (Exception $exc) {
            error_log('Erreur: ' . $exc->getMessage());
            return [];
        }
    }
}
