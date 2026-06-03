<?php

namespace App\Repository;

use App\Entity\Objetcharge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Objetcharge>
 *
 * @method Objetcharge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Objetcharge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Objetcharge[]    findAll()
 * @method Objetcharge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObjetchargeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Objetcharge::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Objetcharge $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Objetcharge $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Objetcharge[] Returns an array of Objetcharge objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Objetcharge
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
