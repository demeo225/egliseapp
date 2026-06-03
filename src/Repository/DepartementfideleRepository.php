<?php

namespace App\Repository;

use App\Entity\Departementfidele;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Departementfidele|null find($id, $lockMode = null, $lockVersion = null)
 * @method Departementfidele|null findOneBy(array $criteria, array $orderBy = null)
 * @method Departementfidele[]    findAll()
 * @method Departementfidele[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepartementfideleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Departementfidele::class);
    }

    // /**
    //  * @return Departementfidele[] Returns an array of Departementfidele objects
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
    public function findOneBySomeField($value): ?Departementfidele
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
