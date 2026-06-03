<?php

namespace App\Repository;

use App\Entity\Presenceculteecodim;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Presenceculteecodim>
 *
 * @method Presenceculteecodim|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presenceculteecodim|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presenceculteecodim[]    findAll()
 * @method Presenceculteecodim[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresenceculteecodimRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Presenceculteecodim::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Presenceculteecodim $entity, bool $flush = true): void {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Presenceculteecodim $entity, bool $flush = true): void {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Presenceculteecodim[] Returns an array of Presenceculteecodim objects
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
      public function findOneBySomeField($value): ?Presenceculteecodim
      {
      return $this->createQueryBuilder('p')
      ->andWhere('p.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function getPresenceEnfantByDates() {
        return $this->createQueryBuilder('m')
                        ->leftJoin('App\Entity\Classecodim', 'e')
                        ->leftJoin('App\Entity\Cultecodim', 'c')
                        ->leftJoin('App\Entity\Eglise', 'eg')
                        ->select('DISTINCT c.dateculte')
                        ->where('c.id :=e.id ')
                        ->where('c.deletedAt IS  NULL')
                        ->andWhere("m.eglise =:eglise")
                        ->orderBy('c.dateculte', 'DESC')
                        ->getQuery()
                        ->getResult();
    }

}
