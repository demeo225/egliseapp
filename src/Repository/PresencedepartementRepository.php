<?php

namespace App\Repository;

use App\Entity\Presencedepartement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Presencedepartement>
 *
 * @method Presencedepartement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presencedepartement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presencedepartement[]    findAll()
 * @method Presencedepartement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresencedepartementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presencedepartement::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Presencedepartement $entity, bool $flush = true): void
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
    public function remove(Presencedepartement $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Presencedepartement[] Returns an array of Presencedepartement objects
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
    public function findOneBySomeField($value): ?Presencedepartement
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
                ->join('App\Entity\Seancedepartement', 'c')
                        ->select('DISTINCT c.datesuper')
                        ->where('m.deletedAt IS  NULL')
                        ->orderBy('c.datesuper', 'DESC')
                        ->getQuery()
                        ->getResult();
    }
    
         public function findOneByPresencedepartement($id): ?Presencedepartement {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
}
