<?php

namespace App\Repository;

use App\Entity\Officiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Officiant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Officiant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Officiant[]    findAll()
 * @method Officiant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Officiant::class);
    }

    // /**
    //  * @return Officiant[] Returns an array of Officiant objects
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
    public function findOneBySomeField($value): ?Officiant
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
