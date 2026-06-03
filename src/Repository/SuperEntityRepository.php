<?php

namespace App\Repository;

use App\Entity\SuperEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SuperEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuperEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuperEntity[]    findAll()
 * @method SuperEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuperEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuperEntity::class);
    }

    // /**
    //  * @return SuperEntity[] Returns an array of SuperEntity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SuperEntity
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
