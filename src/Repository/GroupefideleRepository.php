<?php

namespace App\Repository;

use App\Entity\Groupefidele;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Groupefidele|null find($id, $lockMode = null, $lockVersion = null)
 * @method Groupefidele|null findOneBy(array $criteria, array $orderBy = null)
 * @method Groupefidele[]    findAll()
 * @method Groupefidele[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupefideleRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Groupefidele::class);
    }

    // /**
    //  * @return Groupefidele[] Returns an array of Groupefidele objects
    //  */

    public function getListeFideleByDepartement($nomdepart) {
        return $this->createQueryBuilder('g')
                        ->join('App\Entity\Groupe', 'e', Join::WITH, 'g.groupe = e.id')
                        ->join('App\Entity\Departement', 'd', Join::WITH, 'e.departement = d.id')
                        ->andWhere('d.id = :val')
                        ->andWhere('g.etatgroupe = 1')
                        ->setParameter('val', $nomdepart)
                        ->orderBy('g.id', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function getListeFideleByGroupe($nomgroupe) {
        return $this->createQueryBuilder('g')
                        ->join('App\Entity\Groupe', 'e', Join::WITH, 'g.groupe = e.id')
//            ->join('App\Entity\Departement', 'd', Join::WITH , 'e.departement = d.id')   
                        ->andWhere('e.id = :val')
                        ->andWhere('g.etatgroupe = 1')
                        ->setParameter('val', $nomgroupe)
                        ->orderBy('g.id', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    

    
    /*
      public function findOneBySomeField($value): ?Groupefidele
      {
      return $this->createQueryBuilder('g')
      ->andWhere('g.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
