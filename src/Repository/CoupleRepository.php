<?php

namespace App\Repository;

use App\Entity\Couple;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Couple|null find($id, $lockMode = null, $lockVersion = null)
 * @method Couple|null findOneBy(array $criteria, array $orderBy = null)
 * @method Couple[]    findAll()
 * @method Couple[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoupleRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Couple::class);
    }

// /**
//  * @return Couple[] Returns an array of Couple objects
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
      public function findOneBySomeField($value): ?Couple
      {
      return $this->createQueryBuilder('c')
      ->andWhere('c.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function findwithCouple(int $id) {
        return $this->createQuaryBuilder('c')
                        ->select('c, f')
                        ->join('c.fidele', 'f')
                        ->where('f.id = :NULL')
                        ->setParameter($id)
                        ->getQuery()
                        ->getSingleResult();
    }

}
