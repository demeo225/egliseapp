<?php

namespace App\Repository;

use App\Entity\Cotisercellule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cotisercellule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cotisercellule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cotisercellule[]    findAll()
 * @method Cotisercellule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotisercelluleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotisercellule::class);
    }

    // /**
    //  * @return Cotisercellule[] Returns an array of Cotisercellule objects
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
    public function findOneBySomeField($value): ?Cotisercellule
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
          public function findOneByCotisercellule($id): ?Cotisercellule {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
}
