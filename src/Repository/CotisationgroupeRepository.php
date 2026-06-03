<?php

namespace App\Repository;

use App\Entity\Cotisationgroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Cotisationgroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cotisationgroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cotisationgroupe[]    findAll()
 * @method Cotisationgroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotisationgroupeRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Cotisationgroupe::class);
    }



    // /**
    //  * @return Cotisationgroupe[] Returns an array of Cotisationgroupe objects
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
    
        
        /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Cotisationgroupe $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /*
      public function findOneBySomeField($value): ?Cotisationgroupe
      {
      return $this->createQueryBuilder('c')
      ->andWhere('c.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function findOneByCotisationgroupe($id): ?Cotisationgroupe {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

}
