<?php

namespace App\Repository;

use App\Entity\Classecodim;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Classecodim|null find($id, $lockMode = null, $lockVersion = null)
 * @method Classecodim|null findOneBy(array $criteria, array $orderBy = null)
 * @method Classecodim[]    findAll()
 * @method Classecodim[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassecodimRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classecodim::class);
    }

    // /**
    //  * @return Classecodim[] Returns an array of Classecodim objects
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
    public function findOneBySomeField($value): ?Classecodim
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
