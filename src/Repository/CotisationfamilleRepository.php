<?php

namespace App\Repository;

use App\Entity\Cotisationfamille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cotisationfamille|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cotisationfamille|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cotisationfamille[]    findAll()
 * @method Cotisationfamille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotisationfamilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotisationfamille::class);
    }

    // /**
    //  * @return Cotisationfamille[] Returns an array of Cotisationfamille objects
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
    public function remove(Cotisationfamille $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /*
    public function findOneBySomeField($value): ?Cotisationfamille
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
          public function findOneByCotisationfamille($id): ?Cotisationfamille {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
}
