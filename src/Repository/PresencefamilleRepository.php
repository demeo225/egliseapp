<?php

namespace App\Repository;

use App\Entity\Presencefamille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Presencefamille>
 *
 * @method Presencefamille|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presencefamille|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presencefamille[]    findAll()
 * @method Presencefamille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresencefamilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presencefamille::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Presencefamille $entity, bool $flush = true): void
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
    public function remove(Presencefamille $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Presencefamille[] Returns an array of Presencefamille objects
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
    public function findOneBySomeField($value): ?Presencefamille
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
                ->join('App\Entity\Seancefamille', 'c')
                        ->select('DISTINCT c.datesuper')
                        ->where('m.deletedAt IS  NULL')
                        ->orderBy('c.datesuper', 'DESC')
                        ->getQuery()
                        ->getResult();
    }
    
            public function findOneByPresencefamille($id): ?Presencefamille {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
}
