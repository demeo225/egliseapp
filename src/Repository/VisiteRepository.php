<?php

namespace App\Repository;

use App\Entity\Communaute;
use App\Entity\Eglise;
use App\Entity\Visite;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Visite>
 *
 * @method Visite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visite[]    findAll()
 * @method Visite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisiteRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Visite::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Visite $entity, bool $flush = true): void {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Visite $entity, bool $flush = true): void {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Visite[] Returns an array of Visite objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('v')
      ->andWhere('v.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('v.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    public function rechercheVisite($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
        try {
            $qb = $this->createQueryBuilder('enf');

            $qb->where("enf.deletedAt IS NULL");
            $qb->andWhere("enf.eglise =:eglise");
            $qb->setParameter('eglise', $criteria['eglise']);

            if ($dateDebut && $dateFin) {
                $qb
                        ->andWhere('enf.datevisite BETWEEN :dateDebut AND :dateFin')
                        ->setParameter('dateDebut', $dateDebut)
                        ->setParameter('dateFin', $dateFin);
            }

            if (array_key_exists('receptionpar', $criteria)) {
                $qb->andWhere('enf.receptionpar =:receptionpar');
                $qb->setParameter('receptionpar', $criteria['receptionpar']->getId());
                unset($criteria['receptionpar']);
            }

            //SI on a sexe dans les critere de recherche
            if (array_key_exists('sexe', $criteria)) {

                $qb->andWhere("enf.sexe = :sexe");
                $qb->setParameter('sexe', $criteria['sexe']);
                unset($criteria['sexe']);
            }


            if ($limit) {
                $qb->setMaxResults($limit);
            }

            $query = $qb->getQuery();
            return $query->getResult();
        } catch (Exception $exc) {
            ob_start();
            echo $exc->getMessage();
            $content = ob_get_clean();
            file_put_contents("erreur_rfigerche_figurer.txt", $content . "\n", FILE_APPEND);
            return [];
        }
    }

    public function rechercheVisiten($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
        try {
            $qb = $this->createQueryBuilder('enf');

            $qb->join(Eglise::class, 'e', 'WITH', 'e.id = enf.eglise');
            $qb->join(Communaute::class, 'c', 'WITH', 'c.id = e.communaute');
            $qb->where("enf.deletedAt IS NULL");
            if (array_key_exists('communaute', $criteria)) {

                $qb->andWhere("c.id = :communaute");
                $qb->setParameter('communaute', $criteria['communaute']);
                unset($criteria['communaute']);
            }

            if ($dateDebut && $dateFin) {
                $qb
                        ->andWhere('enf.datevisite BETWEEN :dateDebut AND :dateFin')
                        ->setParameter('dateDebut', $dateDebut)
                        ->setParameter('dateFin', $dateFin);
            }


            if ($limit) {
                $qb->setMaxResults($limit);
            }

            $query = $qb->getQuery();
            return $query->getResult();
        } catch (Exception $exc) {
            ob_start();
            echo $exc->getMessage();
            $content = ob_get_clean();
            file_put_contents("erreur_rfigerche_figurer.txt", $content . "\n", FILE_APPEND);
            return [];
        }
    }

    /*
      public function findOneBySomeField($value): ?Visite
      {
      return $this->createQueryBuilder('v')
      ->andWhere('v.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function getVisiteByDates() {
        return $this->createQueryBuilder('m')
                        ->select('DISTINCT m.datevisite')
                        ->where('m.deletedAt IS  NULL')
                        ->orderBy('m.datevisite', 'DESC')
                        ->getQuery()
                        ->getResult();
    }

}
