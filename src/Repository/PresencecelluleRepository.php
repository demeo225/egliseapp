<?php

namespace App\Repository;

use App\Entity\Presencecellule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Presencecellule>
 *
 * @method Presencecellule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presencecellule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presencecellule[]    findAll()
 * @method Presencecellule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresencecelluleRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Presencecellule::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Presencecellule $entity, bool $flush = true): void {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Presencecellule $entity, bool $flush = true): void {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Presencecellule[] Returns an array of Presencecellule objects
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
      public function findOneBySomeField($value): ?Presencecellule
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
                ->join('App\Entity\Seancecellule', 'c')
                        ->select('DISTINCT c.datesuper')
                        ->where('m.deletedAt IS  NULL')
                        ->orderBy('c.datesuper', 'DESC')
                        ->getQuery()
                        ->getResult();
    }

    public function findOneByPresencellule($id): ?Presencellule {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

}
