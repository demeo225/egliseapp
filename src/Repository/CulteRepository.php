<?php

namespace App\Repository;

use App\Entity\Communaute;
use App\Entity\Culte;
use App\Entity\Eglise;
use App\Entity\Region;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method Culte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Culte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Culte[]    findAll()
 * @method Culte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CulteRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Culte::class);
    }

    // /**
    //  * @return Culte[] Returns an array of Culte objects
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
      public function findOneBySomeField($value): ?Culte
      {
      return $this->createQueryBuilder('c')
      ->andWhere('c.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

public function rechercheCulte(
    array $criteria = [], 
    ?\DateTime $dateDebut = null, 
    ?\DateTime $dateFin = null, 
    ?int $limit = null
): array {
    try {
        $qb = $this->createQueryBuilder('c');
        
        // Important: Vérifier que l'église existe dans les critères
        if (!isset($criteria['eglise'])) {
            return [];
        }
        
        $qb->where("c.deletedAt IS NULL")
           ->andWhere("c.eglise = :eglise")
           ->setParameter('eglise', $criteria['eglise']);
        
        // Gestion des dates
        if ($dateDebut && $dateFin) {
            $dateFinClone = clone $dateFin;
            $dateFinClone->setTime(23, 59, 59);
            
            $qb->andWhere('c.dateculte BETWEEN :dateDebut AND :dateFin')
               ->setParameter('dateDebut', $dateDebut)
               ->setParameter('dateFin', $dateFinClone);
        } elseif ($dateDebut) {
            $qb->andWhere('c.dateculte >= :dateDebut')
               ->setParameter('dateDebut', $dateDebut);
        } elseif ($dateFin) {
            $dateFinClone = clone $dateFin;
            $dateFinClone->setTime(23, 59, 59);
            $qb->andWhere('c.dateculte <= :dateFin')
               ->setParameter('dateFin', $dateFinClone);
        }
        
        // Filtre type culte
        if (!empty($criteria['typeculte'])) {
            $qb->andWhere("c.typeculte = :typeculte")
               ->setParameter('typeculte', $criteria['typeculte']);
        }
        
        // Filtre messager (orateur)
        if (!empty($criteria['messager'])) {
            $qb->andWhere("c.messager = :messager")
               ->setParameter('messager', $criteria['messager']);
        }
        
        // Filtre dirigeant
        if (!empty($criteria['dirigeant'])) {
            $qb->andWhere("c.dirigeant = :dirigeant")
               ->setParameter('dirigeant', $criteria['dirigeant']);
        }
        
        // Tri par date décroissante
        $qb->orderBy('c.dateculte', 'DESC');
        
        if ($limit) {
            $qb->setMaxResults($limit);
        }
        
        return $qb->getQuery()->getResult();
        
    } catch (\Exception $exc) {
        // Logger l'erreur pour Symfony 5.4
        error_log('Erreur recherche culte: ' . $exc->getMessage());
        return [];
    }
}

    public function rechercheCulten($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL)
    {
        try {
            $qb = $this->createQueryBuilder('enf');
            $qb->join(Eglise::class, 'e', 'WITH', 'e.id = enf.eglise');
            $qb->join(Communaute::class, 'c', 'WITH', 'c.id = e.communaute');
            $qb->join(Region::class, 'r', 'WITH', 'r.id = e.region');


            $qb->where("enf.deletedAt IS NULL");
            if (array_key_exists('communaute', $criteria)) {

                $qb->andWhere("c.id = :communaute");
                $qb->setParameter('communaute', $criteria['communaute']);
                unset($criteria['communaute']);
            }
            if (array_key_exists('region', $criteria)) {

                $qb->andWhere("r.id = :region");
                $qb->setParameter('region', $criteria['region']);
                unset($criteria['region']);
            }

            //Tri en fonction des dates debut et fin
            if ($dateDebut && $dateFin) {
                $qb
                    ->andWhere('enf.dateculte BETWEEN :dateDebut AND :dateFin')
                    ->setParameter('dateDebut', $dateDebut)
                    ->setParameter('dateFin', $dateFin);
            }

            if (array_key_exists('typeculte', $criteria)) {

                $qb->andWhere("enf.typeculte = :typeculte");
                $qb->setParameter('typeculte', $criteria['typeculte']);
                unset($criteria['typeculte']);
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

    public function getCultesByDates()
    {
        return $this->createQueryBuilder('m')
            ->select('DISTINCT m.dateculte')
            ->where('m.deletedAt IS  NULL')
            ->orderBy('m.dateculte', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findSommeNombresParDate($eglise): array
    {
        return $this->createQueryBuilder('c')
            ->select([
                'c.dateculte',
                'SUM(c.nmbrehomme) as sommeNombre1',
                'SUM(c.nobrefemme) as sommeNombre2',
                'SUM(c.nbrefant) as sommeNombre3',
                'SUM(c.invite) as sommeNombre4'
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

        public function findInviteParDate($eglise): array
    {
        return $this->createQueryBuilder('c')
            ->select([
                'c.dateculte',

                'SUM(c.invite) as invite'
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

       public function findAdultesParDate($eglise): array
    {
        return $this->createQueryBuilder('c')
            ->select([
                'c.dateculte',
                'SUM(c.nmbrehomme) as homme',
                'SUM(c.nobrefemme) as femme',

            ])
            ->where('c.deletedAt IS  NULL')
             ->andWhere('c.eglise = :eglise')
            ->groupBy('c.dateculte')
            ->orderBy('c.dateculte', 'ASC')
             ->setParameter('eglise', $eglise)
             ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
   
}
