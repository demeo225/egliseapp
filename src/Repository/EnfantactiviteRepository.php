<?php

namespace App\Repository;

use App\Entity\Enfantactivite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Enfantactivite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enfantactivite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enfantactivite[]    findAll()
 * @method Enfantactivite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnfantactiviteRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Enfantactivite::class);
    }

    // /**
    //  * @return Enfantactivite[] Returns an array of Enfantactivite objects
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
      public function findOneBySomeField($value): ?Enfantactivite
      {
      return $this->createQueryBuilder('e')
      ->andWhere('e.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function participantByActivite($participant) {
        return $this->createQueryBuilder('e')
                        ->Join('App\Entity\Enfant', 'f', Join::WITH, 'e.enfant = f.id')
                        ->Join('App\Entity\Ecodimactivite', 'a', Join::WITH, 'e.ecodimactivitie = a.id')
                        ->andWhere('f.id = :val')
                        ->setParameter('val', $participant)
                        ->orderBy('e.id', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function findOneByEnfantactivite($id): ?Enfantactivite {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

}
