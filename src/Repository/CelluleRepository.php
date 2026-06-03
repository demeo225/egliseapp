<?php

namespace App\Repository;

use App\Entity\Cellule;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cellule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cellule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cellule[]    findAll()
 * @method Cellule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CelluleRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Cellule::class);
    }

    // /**
    //  * @return Cellule[] Returns an array of Cellule objects
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
      public function findOneBySomeField($value): ?Cellule
      {
      return $this->createQueryBuilder('c')
      ->andWhere('c.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function membreCellule($cellule) {

        $query = $this->createQueryBuilder('a')
                ->from('App\Entity\Cellule', 'a')
                ->innerJoin('a.fidele', 'u')
                ->where('u.id = :id')
                ->setParameter('id', $cellule->getId());

        return $query;
    }

    public function findOwnedBy($cellule) {
        return $this->membreCellule($cellule)
                        ->getQuery()
                        ->getResult();
    }

        public function findOneCellule($id): ?Cellule {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

      
          public function findOneByUser(User $user): ?Cellule
        {
            return $this->createQueryBuilder('z')
                ->innerJoin('z.users', 'u')
                ->where('u = :user')
                ->andWhere('z.deletedAt IS NULL')
                ->setParameter('user', $user)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }
}
