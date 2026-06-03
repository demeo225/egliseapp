<?php

namespace App\Repository;

use App\Entity\Cotiserfamille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cotiserfamille|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cotiserfamille|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cotiserfamille[]    findAll()
 * @method Cotiserfamille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotiserfamilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotiserfamille::class);
    }

    // /**
    //  * @return Cotiserfamille[] Returns an array of Cotiserfamille objects
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
    public function findOneBySomeField($value): ?Cotiserfamille
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
          public function findOneByCotiserfamille($id): ?Cotiserfamille {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
}
