<?php

namespace App\Repository;

use App\Entity\Communaute;
use App\Entity\Eglise;
use App\Entity\Visite2;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @extends ServiceEntityRepository<Visite2>
 *
 * @method Visite2|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visite2|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visite2[]    findAll()
 * @method Visite2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Visite2Repository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Visite2::class);
    }

    public function add(Visite2 $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Visite2 $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function rechercheVisite2($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
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
            if (array_key_exists('fidele', $criteria)) {
                $qb->andWhere('enf.fidele =:fidele');
                $qb->setParameter('fidele', $criteria['fidele']->getId());
                unset($criteria['fidele']);
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

    public function rechercheVisite2n($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
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
            //SI on a sexe dans les critere de recherche
            if (array_key_exists('sexe', $criteria)) {

                $qb->andWhere("enf.sexe = :sexe");
                $qb->setParameter('sexe', $criteria['sexe']);
                unset($criteria['sexe']);
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

//    /**
//     * @return Visite2[] Returns an array of Visite2 objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
//    public function findOneBySomeField($value): ?Visite2
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
