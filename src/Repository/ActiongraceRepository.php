<?php

namespace App\Repository;

use App\Entity\Actiongrace;
use App\Entity\Communaute;
use App\Entity\Eglise;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method Actiongrace|null find($id, $lockMode = null, $lockVersion = null)
 * @method Actiongrace|null findOneBy(array $criteria, array $orderBy = null)
 * @method Actiongrace[]    findAll()
 * @method Actiongrace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActiongraceRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Actiongrace::class);
    }

    // /**
    //  * @return Actiongrace[] Returns an array of Actiongrace objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('a')
      ->andWhere('a.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('a.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?Actiongrace
      {
      return $this->createQueryBuilder('a')
      ->andWhere('a.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function rechercheActiongrace($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
        try {
            $qb = $this->createQueryBuilder('enf');
            $qb->where("enf.deletedAt IS NULL");
            $qb->andWhere("enf.eglise =:eglise");
            $qb->setParameter('eglise', $criteria['eglise']);

            if ($dateDebut && $dateFin) {
                $qb
                        ->andWhere('enf.dateactiongrace BETWEEN :dateDebut AND :dateFin')
                        ->setParameter('dateDebut', $dateDebut)
                        ->setParameter('dateFin', $dateFin);
            }
            if (array_key_exists('fidele', $criteria)) {
                $qb->andWhere('enf.fidele =:fidele');
                $qb->setParameter('fidele', $criteria['fidele']->getId());
                unset($criteria['fidele']);
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

       public function rechercheActiongracen($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
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
                        ->andWhere('enf.dateactiongrace BETWEEN :dateDebut AND :dateFin')
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

    
    public function getActionByDates() {
        return $this->createQueryBuilder('m')
                        ->select('DISTINCT m.dateactiongrace')
                        ->where('m.deletedAt IS  NULL')
                        ->orderBy('m.dateactiongrace', 'DESC')
                        ->getQuery()
                        ->getResult();
    }

}
