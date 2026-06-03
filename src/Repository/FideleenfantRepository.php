<?php

namespace App\Repository;

use App\Entity\Fideleenfant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fideleenfant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fideleenfant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fideleenfant[]    findAll()
 * @method Fideleenfant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FideleenfantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fideleenfant::class);
    }

    // /**
    //  * @return Fideleenfant[] Returns an array of Fideleenfant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fideleenfant
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
