<?php

namespace App\Repository;

use App\Entity\Seancecellule;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method Seancecellule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seancecellule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seancecellule[]    findAll()
 * @method Seancecellule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeancecelluleRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Seancecellule::class);
    }

    // /**
    //  * @return Seancecellule[] Returns an array of Seancecellule objects
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
      public function findOneBySomeField($value): ?Seancecellule
      {
      return $this->createQueryBuilder('s')
      ->andWhere('s.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */



    public function rechercheCellule($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
        try {
            $qb = $this->createQueryBuilder('enf');
//             $qb->join('App\Entity\Zone', 'ope', Join::WITH, 'enf.zone = ope.id');
//              $qb->join('App\Entity\Nationalite', 'des', Join::WITH, 'enf.nationalite = des.id');
//              $qb->join('App\Entity\Fonction', 'prov', Join::WITH, 'enf.fonction = prov.id');
//              $qb->join('App\Entity\Ethnie', 'prod', Join::WITH, 'enf.ethnie = prod.id');
//              $qb->join('App\Entity\Famille', 'tran', Join::WITH, 'enf.famille = tran.id');
//              $qb->join('App\Entity\Cellule', 'four', Join::WITH, 'enf.cellule = four.id');
//              $qb->join('App\Entity\Quartier', 'clt', Join::WITH, 'enf.quartier = clt.id'); 
            $qb->where("enf.deletedAt IS NULL");
           // $qb->andWhere("enf.eglise =:eglise");
           // $qb->setParameter('eglise', $criteria['eglise']);
//            $qb->addOrderBy('enf.date2', 'DESC');
            //Tri en fonction des dates debut et fin
            if ($dateDebut && $dateFin) {
                $qb
                        ->andWhere('enf.datesuper BETWEEN :dateDebut AND :dateFin')
                        ->setParameter('dateDebut', $dateDebut)
                        ->setParameter('dateFin', $dateFin);
            }

            //SI on a cellule dans les critere de recherche
            if (array_key_exists('cellule', $criteria)) {

                $qb->andWhere("enf.cellule = :cellule");
                $qb->setParameter('cellule', $criteria['cellule']->getId());
                unset($criteria['cellule']);
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

          public function getSeanceByDate($eglise): array
    {
               return $this->createQueryBuilder('m')
                        ->select('DISTINCT m.datesuper')
                        ->where('m.deletedAt IS  NULL')
                         ->andWhere('m.eglise = :eglise')
                        ->orderBy('m.datesuper', 'DESC')
                         ->setParameter('eglise', $eglise)
                        ->setMaxResults(5)
                        ->getQuery()
                        ->getResult();
    } 

       public function findPresenteByDate($eglise): array
    {
        return $this->createQueryBuilder('c')
            ->select([
                'c.datesuper',
                'SUM(c.nbrepresent + c.enfant + c.femme) as totalPresentcellule'
            ])
            ->where('c.deletedAt IS  NULL')
             ->andWhere('c.eglise = :eglise')
            ->groupBy('c.datesuper')
            ->orderBy('c.datesuper', 'DESC')
             ->setParameter('eglise', $eglise)
             ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
 
}
