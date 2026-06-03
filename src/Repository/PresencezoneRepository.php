<?php

namespace App\Repository;

use App\Entity\Presencezone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Presencezone>
 *
 * @method Presencezone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presencezone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presencezone[]    findAll()
 * @method Presencezone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresencezoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presencezone::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Presencezone $entity, bool $flush = true): void
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
    public function remove(Presencezone $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Presencezone[] Returns an array of Presencezone objects
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
    public function findOneBySomeField($value): ?Presencezone
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
                ->join('App\Entity\Seancezone', 'c')
                        ->select('DISTINCT c.datesuper')
                        ->where('m.deletedAt IS  NULL')
                        ->orderBy('c.datesuper', 'DESC')
                        ->getQuery()
                        ->getResult();
    }
    
            public function findOneByPresencezone($id): ?Presencezone {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
}
