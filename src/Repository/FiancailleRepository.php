<?php

namespace App\Repository;

use App\Entity\Fiancaille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fiancaille|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fiancaille|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fiancaille[]    findAll()
 * @method Fiancaille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FiancailleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fiancaille::class);
    }

    // /**
    //  * @return Fiancaille[] Returns an array of Fiancaille objects
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
    public function findOneBySomeField($value): ?Fiancaille
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
