<?php

namespace App\Repository;

use App\Entity\Cotiser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cotiser|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cotiser|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cotiser[]    findAll()
 * @method Cotiser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotiserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotiser::class);
    }

    // /**
    //  * @return Cotiser[] Returns an array of Cotiser objects
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
    public function findOneBySomeField($value): ?Cotiser
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
