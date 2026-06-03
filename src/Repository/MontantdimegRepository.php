<?php

namespace App\Repository;

use App\Entity\Montantdimeg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Montantdimeg>
 *
 * @method Montantdimeg|null find($id, $lockMode = null, $lockVersion = null)
 * @method Montantdimeg|null findOneBy(array $criteria, array $orderBy = null)
 * @method Montantdimeg[]    findAll()
 * @method Montantdimeg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MontantdimegRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Montantdimeg::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Montantdimeg $entity, bool $flush = true): void
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
    public function remove(Montantdimeg $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Montantdimeg[] Returns an array of Montantdimeg objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Montantdimeg
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
           public function findOneByMontant($id): ?Montantdimeg {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
}
