<?php

namespace App\Repository;

use App\Entity\Bapteme;
use App\Entity\Communaute;
use App\Entity\Eglise;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;
use UI\Draw\Line\Join;

/**
 * @method Bapteme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bapteme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bapteme[]    findAll()
 * @method Bapteme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaptemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bapteme::class);
    }

    // /**
    //  * @return Bapteme[] Returns an array of Bapteme objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bapteme
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
       public function rechercheBapteme($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
        try {
            $qb = $this->createQueryBuilder('enf');

            $qb->where("enf.deletedAt IS NULL");
            $qb->andWhere("enf.eglise =:eglise");
            $qb->setParameter('eglise', $criteria['eglise']);

            if ($dateDebut && $dateFin) {
                $qb
                        ->andWhere('enf.datebapteme BETWEEN :dateDebut AND :dateFin')
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

           public function rechercheBaptemen($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
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
                        ->andWhere('enf.datebapteme BETWEEN :dateDebut AND :dateFin')
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

    
     public function getListeFideleByStutbapteme($baptise)
    {
        return $this->createQueryBuilder('g')
            ->join('App\Entity\Fidele', 'f', Join::WITH , 'g.fidele =f.id' )    
            ->andWhere('f.id = :val')
            ->andWhere('f.stutbapteme = :0')
            ->setParameter('val', $baptise)
            ->orderBy('g.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
