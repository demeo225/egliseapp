<?php

namespace App\Repository;

use App\Entity\Communaute;
use App\Entity\Eglise;
use App\Entity\Enfant;
use App\Entity\Region;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method Enfant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enfant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enfant[]    findAll()
 * @method Enfant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnfantRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Enfant::class);
    }

    // /**
    //  * @return Enfant[] Returns an array of Enfant objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('e')
      ->andWhere('e.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('e.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

     // src/Repository/EnfantRepository.php

public function getStatsByEglise($eglise): array
{
    $qb = $this->createQueryBuilder('e');
    
    // Requête pour le total
    $total = $qb
        ->select('COUNT(e.id)')
        ->where('e.eglise = :eglise')
        ->andWhere('e.etatenfant = 1')
        ->andWhere('e.deletedAt IS NULL')
        ->setParameter('eglise', $eglise)
        ->getQuery()
        ->getSingleScalarResult();
    
    // Requête pour les filles
    $filles = $this->createQueryBuilder('e')
        ->select('COUNT(e.id)')
        ->where('e.eglise = :eglise')
        ->andWhere('e.etatenfant = 1')
        ->andWhere('e.deletedAt IS NULL')
        ->andWhere('e.sexe = :sexeFille')
        ->setParameter('eglise', $eglise)
        ->setParameter('sexeFille', 'Fille') // Adaptez selon votre enum/choix
        ->getQuery()
        ->getSingleScalarResult();
    
    // Requête pour les garçons
    $garcons = $this->createQueryBuilder('e')
        ->select('COUNT(e.id)')
        ->where('e.eglise = :eglise')
        ->andWhere('e.etatenfant = 1')
        ->andWhere('e.deletedAt IS NULL')
        ->andWhere('e.sexe = :sexeGarcon')
        ->setParameter('eglise', $eglise)
        ->setParameter('sexeGarcon', 'Garçon') // Adaptez selon votre enum/choix
        ->getQuery()
        ->getSingleScalarResult();
    
    return [
        'total' => $total,
        'filles' => $filles,
        'garcons' => $garcons,
    ];
}

    /*
      public function findOneBySomeField($value): ?Enfant
      {
      return $this->createQueryBuilder('e')
      ->andWhere('e.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */

    public function findOneByEnfant($id): ?Enfant {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

    public function rechercheEnfant($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
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
            $qb->andWhere("enf.etatenfant = 1");
            $qb->andWhere("enf.editable = 1");

            $qb->andWhere("enf.eglise =:eglise");
            $qb->setParameter('eglise', $criteria['eglise']);
//            $qb->addOrderBy('enf.date2', 'DESC');
            //Tri en fonction des dates debut et fin
            if ($dateDebut && $dateFin) {
                $qb
                        ->andWhere('enf.datenaiss BETWEEN :dateDebut AND :dateFin')
                        ->setParameter('dateDebut', $dateDebut)
                        ->setParameter('dateFin', $dateFin);
            }
            if (array_key_exists('zone', $criteria)) {
                $qb->andWhere('enf.zone =:zone');
                $qb->setParameter('zone', $criteria['zone']->getId());
                unset($criteria['zone']);
            }
            //si l'etat de operation est dans les critÃ¨res de recherche
            if (array_key_exists('nationalite', $criteria)) {
                $qb->andWhere("enf.nationalite = :nationalite");
                $qb->setParameter('nationalite', $criteria['nationalite']);
                unset($criteria['nationalite']);
            }


            //SI on a famille dans les critere de recherche
            if (array_key_exists('famille', $criteria)) {
                $qb->andWhere("enf.famille = :famille");
                $qb->setParameter('famille', $criteria['famille']->getId());
                unset($criteria['famille']);
            }

            //SI on a famille dans les critere de recherche
            if (array_key_exists('commune', $criteria)) {
                $qb->andWhere("enf.commune = :commune");
                $qb->setParameter('commune', $criteria['commune']);
                unset($criteria['commune']);
            }


            //SI on a quartier dans les critere de recherche
            if (array_key_exists('quartier', $criteria)) {
//                if($criteria['quartier']){
                $qb->andWhere("enf.quartier = :quartier");
                $qb->setParameter('quartier', $criteria['quartier']->getId());
                unset($criteria['quartier']);
//                }
            }


            //SI on a quartier dans les critere de recherche
            if (array_key_exists('ethnie', $criteria)) {
                $qb->andWhere("enf.ethnie = :ethnie");
                $qb->setParameter('ethnie', $criteria['ethnie']->getId());
                unset($criteria['ethnie']);
//                }
            }

            //SI on a cellule dans les critere de recherche
            if (array_key_exists('cellule', $criteria)) {

                $qb->andWhere("enf.cellule = :cellule");
                $qb->setParameter('cellule', $criteria['cellule']->getId());
                unset($criteria['cellule']);
            }
            //SI on a sexe dans les critere de recherche
            if (array_key_exists('sexe', $criteria)) {

                $qb->andWhere("enf.sexe = :sexe");
                $qb->setParameter('sexe', $criteria['sexe']);
                unset($criteria['sexe']);
            }

            //SI on a sexe dans les critere de recherche
            if (array_key_exists('maladie', $criteria)) {

                $qb->andWhere("enf.maladie = :maladie");
                $qb->setParameter('maladie', $criteria['maladie']);
                unset($criteria['maladie']);
            }

            //SI on a domaineactivite dans les critere de recherche
            if (array_key_exists('niveauetude', $criteria)) {

                $qb->andWhere("enf.niveauetude = :niveauetude");
                $qb->setParameter('niveauetude', $criteria['niveauetude']);
                unset($criteria['niveauetude']);
            }



            //SI on a groupesang dans les critere de recherche
            if (array_key_exists('groupesang', $criteria)) {

                $qb->andWhere("enf.groupesang = :groupesang");
                $qb->setParameter('groupesang', $criteria['groupesang']);
                unset($criteria['groupesang']);
            }


            //SI on a lieuvivre dans les critere de recherche
            if (array_key_exists('lieuvivre', $criteria)) {

                $qb->andWhere("enf.lieuvivre = :lieuvivre");
                $qb->setParameter('lieuvivre', $criteria['lieuvivre']);
                unset($criteria['lieuvivre']);
            }

            //SI on a etatparent dans les critere de recherche
            if (array_key_exists('vieparent', $criteria)) {

                $qb->andWhere("enf.vieparent = :vieparent");
                $qb->setParameter('vieparent', $criteria['vieparent']);
                unset($criteria['vieparent']);
            }
            //SI on a situation dans les critere de recherche
            if (array_key_exists('situation', $criteria)) {

                $qb->andWhere("enf.situation = :situation");
                $qb->setParameter('situation', $criteria['situation']);
                unset($criteria['situation']);
            }
            //SI on a handicap dans les critere de recherche
            if (array_key_exists('handicap', $criteria)) {

                $qb->andWhere("enf.handicap = :handicap");
                $qb->setParameter('handicap', $criteria['handicap']);
                unset($criteria['handicap']);
            }

            //SI on a situationparent dans les critere de recherche
            if (array_key_exists('situationparent', $criteria)) {

                $qb->andWhere("enf.situationparent = :situationparent");
                $qb->setParameter('situationparent', $criteria['situationparent']);
                unset($criteria['situationparent']);
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

    public function rechercheEnfantnational($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL) {
        try {
            $qb = $this->createQueryBuilder('enf');
           // $qb->join(Eglise::class, 'e',  'MEMBER OF c.eglises');
            $qb->join(Eglise::class, 'e', 'WITH', 'e.id = enf.eglise');
            $qb->join(Communaute::class, 'c', 'WITH', 'c.id = e.communaute');
            $qb->join(Region::class, 'r', 'WITH', 'r.id = e.region');

            if (array_key_exists('region', $criteria)) {

                $qb->andWhere("r.id = :reg");
                $qb->setParameter('reg', $criteria['region']);
                unset($criteria['region']);
            }
           
            if (array_key_exists('communaute', $criteria)) {
                $qb->andWhere('e.communaute =:communaute');
                $qb->setParameter('communaute', $criteria['communaute']);
                unset($criteria['communaute']);
            }

            if ($dateDebut && $dateFin) {
                $qb
                        ->andWhere('enf.datenaiss BETWEEN :dateDebut AND :dateFin')
                        ->setParameter('dateDebut', $dateDebut)
                        ->setParameter('dateFin', $dateFin);
            }
        
            //si l'etat de operation est dans les critÃ¨res de recherche
            if (array_key_exists('nationalite', $criteria)) {
                $qb->andWhere("enf.nationalite = :nationalite");
                $qb->setParameter('nationalite', $criteria['nationalite']);
                unset($criteria['nationalite']);
            }


            //SI on a sexe dans les critere de recherche
            if (array_key_exists('sexe', $criteria)) {

                $qb->andWhere("enf.sexe = :sexe");
                $qb->setParameter('sexe', $criteria['sexe']);
                unset($criteria['sexe']);
            }

            //SI on a sexe dans les critere de recherche
            if (array_key_exists('maladie', $criteria)) {

                $qb->andWhere("enf.maladie = :maladie");
                $qb->setParameter('maladie', $criteria['maladie']);
                unset($criteria['maladie']);
            }

            //SI on a domaineactivite dans les critere de recherche
            if (array_key_exists('niveauetude', $criteria)) {

                $qb->andWhere("enf.niveauetude = :niveauetude");
                $qb->setParameter('niveauetude', $criteria['niveauetude']);
                unset($criteria['niveauetude']);
            }



            //SI on a groupesang dans les critere de recherche
            if (array_key_exists('groupesang', $criteria)) {

                $qb->andWhere("enf.groupesang = :groupesang");
                $qb->setParameter('groupesang', $criteria['groupesang']);
                unset($criteria['groupesang']);
            }


            //SI le enfèle est serviteur ou simple enfèle
            if (array_key_exists('merembre', $criteria)) {

                $qb->andWhere("enf.merembre = :merembre");
                $qb->setParameter('merembre', $criteria['merembre']);
                unset($criteria['merembre']);
            }

            //SI on a peremembre dans les critere de recherche
            if (array_key_exists('peremembre', $criteria)) {

                $qb->andWhere("enf.peremembre = :peremembre");
                $qb->setParameter('peremembre', $criteria['peremembre']);
                unset($criteria['peremembre']);
            }

            //SI on a lieuvivre dans les critere de recherche
            if (array_key_exists('lieuvivre', $criteria)) {

                $qb->andWhere("enf.lieuvivre = :lieuvivre");
                $qb->setParameter('lieuvivre', $criteria['lieuvivre']);
                unset($criteria['lieuvivre']);
            }

            //SI on a etatparent dans les critere de recherche
            if (array_key_exists('vieparent', $criteria)) {

                $qb->andWhere("enf.vieparent = :vieparent");
                $qb->setParameter('vieparent', $criteria['vieparent']);
                unset($criteria['vieparent']);
            }
            //SI on a situation dans les critere de recherche
            if (array_key_exists('situation', $criteria)) {

                $qb->andWhere("enf.situation = :situation");
                $qb->setParameter('situation', $criteria['situation']);
                unset($criteria['situation']);
            }
            //SI on a handicap dans les critere de recherche
            if (array_key_exists('handicap', $criteria)) {

                $qb->andWhere("enf.handicap = :handicap");
                $qb->setParameter('handicap', $criteria['handicap']);
                unset($criteria['handicap']);
            }

            //SI on a situationparent dans les critere de recherche
            if (array_key_exists('situationparent', $criteria)) {

                $qb->andWhere("enf.situationparent = :situationparent");
                $qb->setParameter('situationparent', $criteria['situationparent']);
                unset($criteria['situationparent']);
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



    public function countBySexe(string $sexe, $eglise): int
{
    return $this->createQueryBuilder('a')
        ->select('COUNT(a.id)')
        ->where("a.deletedAt IS NULL")
        ->andWhere('a.sexe = :sexe')
        ->andWhere('a.eglise = :eglise')
        ->setParameter('eglise', $eglise)
        ->setParameter('sexe', $sexe)
       // ->setParameter('eglise', $this->getUser()->getEglise()) // Adaptez selon votre logique
        ->getQuery()
        ->getSingleScalarResult();
}

}
