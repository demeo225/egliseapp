<?php

namespace App\Repository;

use App\Entity\Communaute;
use App\Entity\Cultecodim;
use App\Entity\Eglise;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method Cultecodim|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cultecodim|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cultecodim[]    findAll()
 * @method Cultecodim[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CultecodimRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Cultecodim::class);
    }

    // /**
    //  * @return Cultecodim[] Returns an array of Cultecodim objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('c')
      ->andWhere('c.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('c.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?Cultecodim
      {
      return $this->createQueryBuilder('c')
      ->andWhere('c.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function getCultecodimsByDates() {
        return $this->createQueryBuilder('m')
                        ->select('DISTINCT m.dateculte')
                        ->where('m.deletedAt IS  NULL')
                        ->orderBy('m.dateculte', 'DESC')
                        ->getQuery()
                        ->getResult();
    } 

    public function findCultecodimsByDates() {
        return $this->createQueryBuilder('m')
                        ->select('DISTINCT m.dateculte')
                        ->where('m.deletedAt IS  NULL')
                        ->orderBy('m.dateculte', 'DESC')
                        ->getQuery()
                        ->getResult();
    }

    public function rechercheEcodim($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
        try {
            $qb = $this->createQueryBuilder('enf');

            $qb->where("enf.deletedAt IS NULL");
            $qb->andWhere("enf.eglise =:eglise");
            $qb->setParameter('eglise', $criteria['eglise']);

            if ($dateDebut && $dateFin) {
                $qb
                        ->andWhere('enf.dateculte BETWEEN :dateDebut AND :dateFin')
                        ->setParameter('dateDebut', $dateDebut)
                        ->setParameter('dateFin', $dateFin);
            }

            if (array_key_exists('classecodim', $criteria)) {
                $qb->andWhere('enf.classecodim =:classecodim');
                $qb->setParameter('classecodim', $criteria['classecodim']->getId());
                unset($criteria['classecodim']);
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

    public function rechercheEcodimn($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
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
                        ->andWhere('enf.dateculte BETWEEN :dateDebut AND :dateFin')
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

        public function findSommeNombresParDateEcodim($eglise): array
    {
        return $this->createQueryBuilder('c')
            ->select([
                'c.dateculte',
                'SUM(c.nbregarcon) as sommeNombreecodim1',
                'SUM(c.nbrefille) as sommeNombreecodim2',
      
            ])
            ->where('c.deletedAt IS  NULL')
             ->andWhere('c.eglise = :eglise')
            ->groupBy('c.dateculte')
            ->orderBy('c.dateculte', 'DESC')
             ->setParameter('eglise', $eglise)
             ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

         public function findPresenteGroupeeByDate($eglise): array
    {
        return $this->createQueryBuilder('c')
            ->select([
                'c.dateculte',
                'SUM(c.nbregarcon + c.nbrefille) as totalPresentEcodim'
            ])
            ->where('c.deletedAt IS  NULL')
             ->andWhere('c.eglise = :eglise')
            ->groupBy('c.dateculte')
            ->orderBy('c.dateculte', 'DESC')
             ->setParameter('eglise', $eglise)
             ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

}
