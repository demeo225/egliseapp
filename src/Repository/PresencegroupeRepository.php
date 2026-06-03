<?php

namespace App\Repository;

use App\Entity\Presencegroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Presencegroupe>
 *
 * @method Presencegroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presencegroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presencegroupe[]    findAll()
 * @method Presencegroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresencegroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presencegroupe::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Presencegroupe $entity, bool $flush = true): void
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
    public function remove(Presencegroupe $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Presencegroupe[] Returns an array of Presencegroupe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Presencegroupe
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
        public function getPresenceByDates() {
        return $this->createQueryBuilder('m')
                ->join('App\Entity\Seancegroupe', 'c')
                        ->select('DISTINCT c.datesuper')
                        ->where('m.deletedAt IS  NULL')
                        ->orderBy('c.datesuper', 'DESC')
                        ->getQuery()
                        ->getResult();
    }
    
            public function findOneByPresencegroupe($id): ?Presencegroupe {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
}
