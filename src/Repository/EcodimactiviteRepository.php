<?php

namespace App\Repository;

use App\Entity\Ecodimactivite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ecodimactivite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ecodimactivite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ecodimactivite[]    findAll()
 * @method Ecodimactivite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcodimactiviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ecodimactivite::class);
    }

    // /**
    //  * @return Ecodimactivite[] Returns an array of Ecodimactivite objects
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
    public function findOneBySomeField($value): ?Ecodimactivite
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
      public function findOneByEcodimactivite($id): ?Ecodimactivite {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
}
