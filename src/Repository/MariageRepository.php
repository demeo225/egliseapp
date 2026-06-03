<?php

namespace App\Repository;

use App\Entity\Communaute;
use App\Entity\Eglise;
use App\Entity\Mariage;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method Mariage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mariage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mariage[]    findAll()
 * @method Mariage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MariageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mariage::class);
    }

    // /**
    //  * @return Mariage[] Returns an array of Mariage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Mariage
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
        
        public function rechercheMariage($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
        try {
            $qb = $this->createQueryBuilder('enf');
//             $qb->join('App\Entity\Zone', 'ope', Join::WITH, 'enf.zone = ope.id');
//              $qb->join('App\Entity\Nationalite', 'des', Join::WITH, 'enf.nationalite = des.id');
//              $qb->join('App\Entity\Fonction', 'prov', Join::WITH, 'enf.fonction = prov.id');
//              $qb->join('App\Entity\Ethnie', 'prod', Join::WITH, 'enf.ethnie = prod.id');
//              $qb->join('App\Entity\Famille', 'tran', Join::WITH, 'enf.groupe = tran.id');
//              $qb->join('App\Entity\Cellule', 'four', Join::WITH, 'enf.groupe = four.id');
//              $qb->join('App\Entity\Quartier', 'clt', Join::WITH, 'enf.quartier = clt.id'); 
            $qb->where("enf.deletedAt IS NULL");
            $qb->andWhere("enf.eglise =:eglise");
            $qb->setParameter('eglise', $criteria['eglise']);
//            $qb->addOrderBy('enf.date2', 'DESC');
            //Tri en fonction des dates debut et fin
            if ($dateDebut && $dateFin) {
                $qb
                        ->andWhere('enf.datemariage BETWEEN :dateDebut AND :dateFin')
                        ->setParameter('dateDebut', $dateDebut)
                        ->setParameter('dateFin', $dateFin);
            }
      if (array_key_exists('regime', $criteria)) {

                $qb->andWhere("enf.regime = :regime");
                $qb->setParameter('regime', $criteria['regime']);
                unset($criteria['regime']);
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
    
           public function rechercheMariagen($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
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
                        ->andWhere('enf.datemariage BETWEEN :dateDebut AND :dateFin')
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

}
