<?php

namespace App\Repository;

use App\Entity\DoctrineSchemaUpdate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DoctrineSchemaUpdate|null find($id, $lockMode = null, $lockVersion = null)
 * @method DoctrineSchemaUpdate|null findOneBy(array $criteria, array $orderBy = null)
 * @method DoctrineSchemaUpdate[]    findAll()
 * @method DoctrineSchemaUpdate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrineSchemaUpdateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DoctrineSchemaUpdate::class);
    }

    // /**
    //  * @return DoctrineSchemaUpdate[] Returns an array of DoctrineSchemaUpdate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DoctrineSchemaUpdate
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
