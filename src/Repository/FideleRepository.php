<?php

namespace App\Repository;

use App\Entity\Communaute;
use App\Entity\Departement;
use App\Entity\Eglise;
use App\Entity\Fidele;
use App\Entity\Region;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method Fidele|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fidele|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fidele[]    findAll()
 * @method Fidele[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FideleRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fidele::class);
    }

    public function rechercheFidele($criteria = [], DateTime $dateDebut , DateTime $dateFin , $limit = NULL)
    {
        try {
            $qb = $this->createQueryBuilder('fid');

            $qb->where("fid.deletedAt IS NULL");
            $qb->andWhere("fid.etatfidele = 1");
            $qb->andWhere("fid.editable = 1");

            $qb->andWhere("fid.eglise =:eglise");
            $qb->setParameter('eglise', $criteria['eglise']);
            //            $qb->addOrderBy('fid.date2', 'DESC');
            //Tri en fonction des dates debut et fin
            if ($dateDebut && $dateFin) {
                $qb
                    ->andWhere('fid.datearriver BETWEEN :dateDebut AND :dateFin')
                    ->setParameter('dateDebut', $dateDebut)
                    ->setParameter('dateFin', $dateFin);
            }

            if (array_key_exists('zone', $criteria)) {
                $qb->andWhere('fid.zone =:zone');
                $qb->setParameter('zone', $criteria['zone']->getId());
                unset($criteria['zone']);
            }
            //si l'etat de operation est dans les critÃ¨res de recherche
            if (array_key_exists('nationalite', $criteria)) {
                $qb->andWhere("fid.nationalite = :nationalite");
                $qb->setParameter('nationalite', $criteria['nationalite']);
                unset($criteria['nationalite']);
            }

            //SI on a provenance dans les critere de recherche
            if (array_key_exists('fonction', $criteria)) {
                $qb->andWhere("fid.fonction = :fonction");
                $qb->setParameter('fonction', $criteria['fonction']->getId());
                unset($criteria['fonction']);
                //                dd($criteria['fonction']->getId());
            }
            //SI on a famille dans les critere de recherche
            if (array_key_exists('famille', $criteria)) {
                $qb->andWhere("fid.famille = :famille");
                $qb->setParameter('famille', $criteria['famille']->getId());
                unset($criteria['famille']);
            }

            //SI on a famille dans les critere de recherche
            if (array_key_exists('commune', $criteria)) {
                $qb->andWhere("fid.commune = :commune");
                $qb->setParameter('commune', $criteria['commune']->getId());
                unset($criteria['commune']);
            }


            //SI on a famille dans les critere de recherche
            if (array_key_exists('maladie', $criteria)) {
                $qb->andWhere("fid.maladie = :maladie");
                $qb->setParameter('maladie', $criteria['maladie']->getId());
                unset($criteria['maladie']);
            }



            //SI on a quartier dans les critere de recherche
            if (array_key_exists('quartier', $criteria)) {
                //                if($criteria['quartier']){
                $qb->andWhere("fid.quartier = :quartier");
                $qb->setParameter('quartier', $criteria['quartier']->getId());
                unset($criteria['quartier']);
                //                }
            }


            //SI on a quartier dans les critere de recherche
            if (array_key_exists('ethnie', $criteria)) {
                $qb->andWhere("fid.ethnie = :ethnie");
                $qb->setParameter('ethnie', $criteria['ethnie']->getId());
                unset($criteria['ethnie']);
                //                }
            }

            //SI on a cellule dans les critere de recherche
            if (array_key_exists('cellule', $criteria)) {

                $qb->andWhere("fid.cellule = :cellule");
                $qb->setParameter('cellule', $criteria['cellule']->getId());
                unset($criteria['cellule']);
            }
            //SI on a sexe dans les critere de recherche
            if (array_key_exists('sexe', $criteria)) {

                $qb->andWhere("fid.sexe = :sexe");
                $qb->setParameter('sexe', $criteria['sexe']);
                unset($criteria['sexe']);
            }


            //SI on a domaineactivite dans les critere de recherche
            if (array_key_exists('domaineactivite', $criteria)) {

                $qb->andWhere("fid.domaineactivite = :domaineactivite");
                $qb->setParameter('domaineactivite', $criteria['domaineactivite']);
                unset($criteria['domaineactivite']);
            }

            //SI on a statutmatri dans les critere de recherche
            if (array_key_exists('statutmatri', $criteria)) {

                $qb->andWhere("fid.statutmatri = :statutmatri");
                $qb->setParameter('statutmatri', $criteria['statutmatri']);
                unset($criteria['statutmatri']);
            }

            //SI on a groupesang dans les critere de recherche
            if (array_key_exists('groupesang', $criteria)) {

                $qb->andWhere("fid.groupesang = :groupesang");
                $qb->setParameter('groupesang', $criteria['groupesang']);
                unset($criteria['groupesang']);
            }

            //SI le fidèle est serviteur ou simple fidèle
            if (array_key_exists('typefidele', $criteria)) {

                $qb->andWhere("fid.typefidele = :typefidele");
                $qb->setParameter('typefidele', $criteria['typefidele']);
                unset($criteria['typefidele']);
            }

            //SI le fidèle est serviteur ou simple fidèle
            if (array_key_exists('bapteme', $criteria)) {

                $qb->andWhere("fid.bapteme = :bapteme");
                $qb->setParameter('bapteme', $criteria['bapteme']);
                unset($criteria['bapteme']);
            }



            //SI on a etude dans les critere de recherche
            if (array_key_exists('etude', $criteria)) {

                $qb->andWhere("fid.etude = :etude");
                $qb->setParameter('etude', $criteria['etude']);
                unset($criteria['etude']);
            }


            //SI on a stutbapteme dans les critere de recherche
            if (array_key_exists('stutbapteme', $criteria)) {

                $qb->andWhere("fid.stutbapteme = :stutbapteme");
                $qb->setParameter('stutbapteme', $criteria['stutbapteme']);
                unset($criteria['stutbapteme']);
            }
            //SI on a choiculte dans les critere de recherche
            if (array_key_exists('choiculte', $criteria)) {

                $qb->andWhere("fid.choiculte = :choiculte");
                $qb->setParameter('choiculte', $criteria['choiculte']);
                unset($criteria['choiculte']);
            }
            //SI on a vieseul dans les critere de recherche
            if (array_key_exists('vieseul', $criteria)) {

                $qb->andWhere("fid.vieseul = :vieseul");
                $qb->setParameter('vieseul', $criteria['vieseul']);
                unset($criteria['vieseul']);
            }



            //Permis
            if (array_key_exists('permis', $criteria)) {

                $qb->andWhere("fid.permis = :permis");
                $qb->setParameter('permis', $criteria['permis']);
                unset($criteria['permis']);
            }

            //Emploi
            if (array_key_exists('emploi', $criteria)) {

                $qb->andWhere("fid.emploi = :emploi");
                $qb->setParameter('emploi', $criteria['emploi']);
                unset($criteria['emploi']);
            }


            //SI on a langue dans les critere de recherche
            if (array_key_exists('langue', $criteria)) {

                $qb->andWhere("fid.langue = :langue");
                $qb->setParameter('langue', $criteria['langue']);
                unset($criteria['langue']);
            }
            //SI on a cultefamille dans les critere de recherche
            if (array_key_exists('cultefamille', $criteria)) {

                $qb->andWhere("fid.cultefamille = :cultefamille");
                $qb->setParameter('cultefamille', $criteria['cultefamille']);
                unset($criteria['cultefamille']);
            }
            //SI on a priere dans les critere de recherche
            if (array_key_exists('priere', $criteria)) {

                $qb->andWhere("fid.priere = :priere");
                $qb->setParameter('priere', $criteria['priere']);
                unset($criteria['priere']);
            }



            //SI on a lecture dans les critere de recherche
            if (array_key_exists('lecture', $criteria)) {

                $qb->andWhere("fid.lecture = :lecture");
                $qb->setParameter('lecture', $criteria['lecture']);
                unset($criteria['lecture']);
            }
            //SI on a temoignage dans les critere de recherche
            if (array_key_exists('temoignage', $criteria)) {

                $qb->andWhere("fid.temoignage = :temoignage");
                $qb->setParameter('temoignage', $criteria['temoignage']);
                unset($criteria['temoignage']);
            }


            //SI on a bibleformation dans les critere de recherche
            if (array_key_exists('bibleformation', $criteria)) {

                $qb->andWhere("fid.bibleformation = :bibleformation");
                $qb->setParameter('bibleformation', $criteria['bibleformation']);
                unset($criteria['bibleformation']);
            }
            //SI on a etatparent dans les critere de recherche
            if (array_key_exists('etatparent', $criteria)) {

                $qb->andWhere("fid.etatparent = :etatparent");
                $qb->setParameter('etatparent', $criteria['etatparent']);
                unset($criteria['etatparent']);
            }
            //SI on a situation dans les critere de recherche
            if (array_key_exists('situation', $criteria)) {

                $qb->andWhere("fid.situation = :situation");
                $qb->setParameter('situation', $criteria['situation']);
                unset($criteria['situation']);
            }
            //SI on a handicap dans les critere de recherche
            if (array_key_exists('handicap', $criteria)) {

                $qb->andWhere("fid.handicap = :handicap");
                $qb->setParameter('handicap', $criteria['handicap']);
                unset($criteria['handicap']);
            }

            //SI on a etatvieparent dans les critere de recherche
            if (array_key_exists('etatvieparent', $criteria)) {

                $qb->andWhere("fid.etatvieparent = :etatvieparent");
                $qb->setParameter('etatvieparent', $criteria['etatvieparent']);
                unset($criteria['etatvieparent']);
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

    public function rechercheFidelenational($criteria = [], DateTime $dateDebut , DateTime $dateFin, $limit = NULL)
    {
        try {
            $qb = $this->createQueryBuilder('fid');
            // $qb->join(Eglise::class, 'e',  'MEMBER OF c.eglises');
            $qb->join(Eglise::class, 'e', 'WITH', 'e.id = fid.eglise');
            $qb->join(Communaute::class, 'c', 'WITH', 'c.id = e.communaute');
            $qb->join(Region::class, 'r', 'WITH', 'r.id = e.region');

            $qb->where("fid.deletedAt IS NULL");
            $qb->andWhere("fid.etatfidele = 1");
            $qb->andWhere("fid.editable = 1");

            //            $qb->addOrderBy('fid.date2', 'DESC');
            //Tri en fonction des dates debut et fin
            if ($dateDebut && $dateFin) {
                $qb
                    ->andWhere('fid.datearriver BETWEEN :dateDebut AND :dateFin')
                    ->setParameter('dateDebut', $dateDebut)
                    ->setParameter('dateFin', $dateFin);
            }

            if (array_key_exists('communaute', $criteria)) {

                $qb->andWhere("c.id = :communaute");
                $qb->setParameter('communaute', $criteria['communaute']);
                unset($criteria['communaute']);
            }


            if (array_key_exists('nationalite', $criteria)) {
                $qb->andWhere("fid.nationalite = :nationalite");
                $qb->setParameter('nationalite', $criteria['nationalite']);
                unset($criteria['nationalite']);
            }

            if (array_key_exists('region', $criteria)) {

                $qb->andWhere("r.id = :region");
                $qb->setParameter('region', $criteria['region']);
                unset($criteria['region']);
            }

            if (array_key_exists('sexe', $criteria)) {

                $qb->andWhere("fid.sexe = :sexe");
                $qb->setParameter('sexe', $criteria['sexe']);
                unset($criteria['sexe']);
            }


            //SI on a domaineactivite dans les critere de recherche
            if (array_key_exists('domaineactivite', $criteria)) {

                $qb->andWhere("fid.domaineactivite = :domaineactivite");
                $qb->setParameter('domaineactivite', $criteria['domaineactivite']);
                unset($criteria['domaineactivite']);
            }

            //SI on a statutmatri dans les critere de recherche
            if (array_key_exists('statutmatri', $criteria)) {

                $qb->andWhere("fid.statutmatri = :statutmatri");
                $qb->setParameter('statutmatri', $criteria['statutmatri']);
                unset($criteria['statutmatri']);
            }

            //SI on a groupesang dans les critere de recherche
            if (array_key_exists('groupesang', $criteria)) {

                $qb->andWhere("fid.groupesang = :groupesang");
                $qb->setParameter('groupesang', $criteria['groupesang']);
                unset($criteria['groupesang']);
            }

            //SI le fidèle est serviteur ou simple fidèle
            if (array_key_exists('typefidele', $criteria)) {

                $qb->andWhere("fid.typefidele = :typefidele");
                $qb->setParameter('typefidele', $criteria['typefidele']);
                unset($criteria['typefidele']);
            }

            //SI le fidèle est serviteur ou simple fidèle
            if (array_key_exists('bapteme', $criteria)) {

                $qb->andWhere("fid.bapteme = :bapteme");
                $qb->setParameter('bapteme', $criteria['bapteme']);
                unset($criteria['bapteme']);
            }



            //SI on a etude dans les critere de recherche
            if (array_key_exists('etude', $criteria)) {

                $qb->andWhere("fid.etude = :etude");
                $qb->setParameter('etude', $criteria['etude']);
                unset($criteria['etude']);
            }


            //SI on a stutbapteme dans les critere de recherche
            if (array_key_exists('stutbapteme', $criteria)) {

                $qb->andWhere("fid.stutbapteme = :stutbapteme");
                $qb->setParameter('stutbapteme', $criteria['stutbapteme']);
                unset($criteria['stutbapteme']);
            }
            //SI on a choiculte dans les critere de recherche
            if (array_key_exists('choiculte', $criteria)) {

                $qb->andWhere("fid.choiculte = :choiculte");
                $qb->setParameter('choiculte', $criteria['choiculte']);
                unset($criteria['choiculte']);
            }
            //SI on a vieseul dans les critere de recherche
            if (array_key_exists('vieseul', $criteria)) {

                $qb->andWhere("fid.vieseul = :vieseul");
                $qb->setParameter('vieseul', $criteria['vieseul']);
                unset($criteria['vieseul']);
            }



            //Permis
            if (array_key_exists('permis', $criteria)) {

                $qb->andWhere("fid.permis = :permis");
                $qb->setParameter('permis', $criteria['permis']);
                unset($criteria['permis']);
            }

            //Emploi
            if (array_key_exists('emploi', $criteria)) {

                $qb->andWhere("fid.emploi = :emploi");
                $qb->setParameter('emploi', $criteria['emploi']);
                unset($criteria['emploi']);
            }


            //SI on a langue dans les critere de recherche
            if (array_key_exists('langue', $criteria)) {

                $qb->andWhere("fid.langue = :langue");
                $qb->setParameter('langue', $criteria['langue']);
                unset($criteria['langue']);
            }
            //SI on a cultefamille dans les critere de recherche
            if (array_key_exists('cultefamille', $criteria)) {

                $qb->andWhere("fid.cultefamille = :cultefamille");
                $qb->setParameter('cultefamille', $criteria['cultefamille']);
                unset($criteria['cultefamille']);
            }
            //SI on a priere dans les critere de recherche
            if (array_key_exists('priere', $criteria)) {

                $qb->andWhere("fid.priere = :priere");
                $qb->setParameter('priere', $criteria['priere']);
                unset($criteria['priere']);
            }



            //SI on a lecture dans les critere de recherche
            if (array_key_exists('lecture', $criteria)) {

                $qb->andWhere("fid.lecture = :lecture");
                $qb->setParameter('lecture', $criteria['lecture']);
                unset($criteria['lecture']);
            }
            //SI on a temoignage dans les critere de recherche
            if (array_key_exists('temoignage', $criteria)) {

                $qb->andWhere("fid.temoignage = :temoignage");
                $qb->setParameter('temoignage', $criteria['temoignage']);
                unset($criteria['temoignage']);
            }


            //SI on a bibleformation dans les critere de recherche
            if (array_key_exists('bibleformation', $criteria)) {

                $qb->andWhere("fid.bibleformation = :bibleformation");
                $qb->setParameter('bibleformation', $criteria['bibleformation']);
                unset($criteria['bibleformation']);
            }
            //SI on a etatparent dans les critere de recherche
            if (array_key_exists('etatparent', $criteria)) {

                $qb->andWhere("fid.etatparent = :etatparent");
                $qb->setParameter('etatparent', $criteria['etatparent']);
                unset($criteria['etatparent']);
            }
            //SI on a situation dans les critere de recherche
            if (array_key_exists('situation', $criteria)) {

                $qb->andWhere("fid.situation = :situation");
                $qb->setParameter('situation', $criteria['situation']);
                unset($criteria['situation']);
            }
            //SI on a handicap dans les critere de recherche
            if (array_key_exists('handicap', $criteria)) {

                $qb->andWhere("fid.handicap = :handicap");
                $qb->setParameter('handicap', $criteria['handicap']);
                unset($criteria['handicap']);
            }

            //            //SI on a etatvieparent dans les critere de recherche
            if (array_key_exists('etatvieparent', $criteria)) {

                $qb->andWhere("fid.etatvieparent = :etatvieparent");
                $qb->setParameter('etatvieparent', $criteria['etatvieparent']);
                unset($criteria['etatvieparent']);
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

    public function findOneByFidele($id): ?Fidele
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function remove(Fidele $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function getFideleByEglise($eglise): array
    {
        $qb = $this->createQueryBuilder('e');

        // Requête pour le total
        $total = $qb
            ->select('COUNT(e.id)')
            ->where('e.eglise = :eglise')
            ->andWhere('e.etatfidele = 1')
            ->andWhere('e.deletedAt IS NULL')
            ->setParameter('eglise', $eglise)
            ->getQuery()
            ->getSingleScalarResult();

        // Requête pour les femmes
        $femmes = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.eglise = :eglise')
            ->andWhere('e.etatfidele = 1')
            ->andWhere('e.deletedAt IS NULL')
            ->andWhere('e.sexe = :sexeFille')
            ->setParameter('eglise', $eglise)
            ->setParameter('sexeFille', 'Femme') // Adaptez selon votre enum/choix
            ->getQuery()
            ->getSingleScalarResult();

        // Requête pour les garçons
        $hommes = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.eglise = :eglise')
            ->andWhere('e.etatfidele = 1')
            ->andWhere('e.deletedAt IS NULL')
            ->andWhere('e.sexe = :sexeGarcon')
            ->setParameter('eglise', $eglise)
            ->setParameter('sexeGarcon', 'Homme') // Adaptez selon votre enum/choix
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'totalfidele' => $total,
            'femmes' => $femmes,
            'hommes' => $hommes,
        ];
    }

    public function countBySexe(string $sexe, $eglise): int
{
    return $this->createQueryBuilder('a')
        ->select('COUNT(a.id)')
         ->where("a.deletedAt IS NULL")
        ->andWhere('a.sexe = :sexe')
        ->andWhere('a.eglise = :eglise')
       // ->andWhere('a.eglise = :eglise') // Supposons que vous avez une relation vers l'église
        ->setParameter('sexe', $sexe)
        ->setParameter('eglise', $eglise)
        //->setParameter('eglise', $this->getUser()->getEglise()) // Adaptez selon votre logique
        ->getQuery()
        ->getSingleScalarResult();
}

    public function findByDepartement(Departement $departement): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.groupefideles', 'mg')
            ->join('mg.groupe', 'g')
            ->join('g.departement', 'd')
            ->where('d.id = :departementId')
            ->andWhere('f.etatfidele = 1')
            ->andWhere('f.deletedAt IS NULL')
            ->setParameter('departementId', $departement->getId())
            ->orderBy('f.nomfidele', 'ASC')
            ->addOrderBy('f.nomfidele', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countPatientsByEthnie($eglise)
{
    return $this->createQueryBuilder('p')
        ->select('a.libelle as ethnieName, COUNT(p.id) as fideleCount')
        ->leftJoin('p.ethnie', 'a')
        ->where("a.deletedAt IS NULL")
        ->andWhere('a.eglise = :eglise')
        ->setParameter('eglise', $eglise)
        ->groupBy('a.libelle')
        ->orderBy('fideleCount', 'DESC')
        ->getQuery()
        ->getResult();
}

    public function countPatientsByFonction($eglise)
{
    return $this->createQueryBuilder('p')
        ->select('a.libelle as fonctionName, COUNT(p.id) as fideleCount')
        ->leftJoin('p.fonction', 'a')
        ->where("a.deletedAt IS NULL")
        ->andWhere('a.eglise = :eglise')
        ->setParameter('eglise', $eglise)
        ->groupBy('a.libelle')
        ->orderBy('fideleCount', 'DESC')
        ->getQuery()
        ->getResult();
    }

 public function getFidelesParTrancheDAge($eglise)
    {
        // Calcul de l'âge et définition des tranches
        $qb = $this->createQueryBuilder('f');
        $qb
            ->select(
                "CASE
                    WHEN TIMESTAMPDIFF(YEAR, f.datenaiss, CURRENT_DATE()) BETWEEN 15 AND 24 THEN '15-24'
                    WHEN TIMESTAMPDIFF(YEAR, f.datenaiss, CURRENT_DATE()) BETWEEN 25 AND 64 THEN '25-64'
                    ELSE '65+'
                END AS trancheAge",
                'COUNT(f.id) AS nombreFideles', // Compter le nombre de fidèles
                "f.deletedAt" // Inclure l'état pour le filtre
            )
            ->andWhere('f.deletedAt IS NULL') // Filtrer où l'état est NULL
             ->andWhere('f.eglise = :eglise')
          ->setParameter('eglise', $eglise)
            ->groupBy('trancheAge')
            ->addGroupBy('f.etat')
            ->orderBy('trancheAge');

        return $qb->getQuery()->getResult();
    }

        //Code fidèle
       public function findLastCodeByEglise(Eglise $eglise)
{
    return $this->createQueryBuilder('f')
        ->select('f.code')
        ->where('f.eglise = :eglise')
        ->setParameter('eglise', $eglise)
        ->orderBy('f.id', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
}


        public function findFidelesByGroupe(int $groupeId): array
        {
            return $this->createQueryBuilder('f')
                ->innerJoin('f.groupefideles', 'gf')
                ->innerJoin('gf.groupe', 'g')
                ->where('g.id = :gid')
                ->setParameter('gid', $groupeId)
                ->orderBy('f.nomfidele', 'ASC')
                ->getQuery()
                ->getResult();
        }


        public function findFidelesByDepartement(int $departementId): array
        {
            return $this->createQueryBuilder('f')
                ->innerJoin('f.groupefideles', 'gf')
                ->innerJoin('gf.groupe', 'g')
                ->innerJoin('g.departement', 'd')
                ->where('d.id = :departementId')
                ->andWhere('f.etatfidele = :etat') // si vous avez un champ état
                ->andWhere('f.deletedAt IS NULL') // si vous utilisez soft delete
                ->setParameter('departementId', $departementId)
                ->setParameter('etat', 1) // ou la valeur appropriée
                ->orderBy('f.nomfidele', 'ASC')
                ->getQuery()
                ->getResult();
        }


}
