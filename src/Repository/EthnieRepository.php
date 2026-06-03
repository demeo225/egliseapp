<?php

namespace App\Repository;

use App\Entity\Ethnie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ethnie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ethnie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ethnie[]    findAll()
 * @method Ethnie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EthnieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ethnie::class);
    }

    // /**
    //  * @return Ethnie[] Returns an array of Ethnie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ethnie
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
