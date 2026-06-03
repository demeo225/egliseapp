<?php

namespace App\Repository;

use App\Entity\Enfantfidele;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Enfantfidele|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enfantfidele|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enfantfidele[]    findAll()
 * @method Enfantfidele[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnfantfideleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enfantfidele::class);
    }

    // /**
    //  * @return Enfantfidele[] Returns an array of Enfantfidele objects
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
    public function findOneBySomeField($value): ?Enfantfidele
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
