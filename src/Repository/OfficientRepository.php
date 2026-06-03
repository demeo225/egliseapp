<?php

namespace App\Repository;

use App\Entity\Officient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Officient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Officient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Officient[]    findAll()
 * @method Officient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Officient::class);
    }

    // /**
    //  * @return Officient[] Returns an array of Officient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Officient
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
