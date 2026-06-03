<?php

namespace App\Repository;

use App\Entity\Seancedepartement;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method Seancedepartement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seancedepartement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seancedepartement[]    findAll()
 * @method Seancedepartement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeancedepartementRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Seancedepartement::class);
    }

    // /**
    //  * @return Seancedepartement[] Returns an array of Seancedepartement objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('s')
      ->andWhere('s.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('s.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?Seancedepartement
      {
      return $this->createQueryBuilder('s')
      ->andWhere('s.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function rechercheDepartement($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
        try {
            $qb = $this->createQueryBuilder('enf');
 
            $qb->where("enf.deletedAt IS NULL");

            $qb->andWhere("enf.eglise =:eglise");
            $qb->setParameter('eglise', $criteria['eglise']);
//            $qb->addOrderBy('enf.date2', 'DESC');
            //Tri en fonction des dates debut et fin
            if ($dateDebut && $dateFin) {
                $qb
                        ->andWhere('enf.datesuper BETWEEN :dateDebut AND :dateFin')
                        ->setParameter('dateDebut', $dateDebut)
                        ->setParameter('dateFin', $dateFin);
            }

            //SI on a departement dans les critere de recherche
            if (array_key_exists('departement', $criteria)) {

                $qb->andWhere("enf.departement = :departement");
                $qb->setParameter('departement', $criteria['departement']);
                unset($criteria['departement']);
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

    public function getSeanceByDates() {
        return $this->createQueryBuilder('m')
                        ->select('DISTINCT m.datesuper')
                        ->where('m.deletedAt IS  NULL')
                        ->orderBy('m.datesuper', 'DESC')
                        ->getQuery()
                        ->getResult();
    }

}
