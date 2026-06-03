<?php

namespace App\Repository;

use App\Entity\Objetrecette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Objetrecette>
 *
 * @method Objetrecette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Objetrecette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Objetrecette[]    findAll()
 * @method Objetrecette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObjetrecetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Objetrecette::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Objetrecette $entity, bool $flush = true): void
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
    public function remove(Objetrecette $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Objetrecette[] Returns an array of Objetrecette objects
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
    public function findOneBySomeField($value): ?Objetrecette
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
