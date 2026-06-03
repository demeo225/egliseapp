<?php

namespace App\Repository;

use App\Entity\Cotisergroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
 use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cotisergroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cotisergroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cotisergroupe[]    findAll()
 * @method Cotisergroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotisergroupeRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Cotisergroupe::class);
    }

    // /**
    //  * @return Cotisergroupe[] Returns an array of Cotisergroupe objects
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
      public function findOneBySomeField($value): ?Cotisergroupe
      {
      return $this->createQueryBuilder('c')
      ->andWhere('c.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
     * use Doctrine\ORM\Query\Expr;

->leftJoin('a.installations', 'i', Expr\Join::WITH, 'i.page = :page')
->setParameter('page', $page)
      ;
      }
     */


    public function getListeCotisationGroupe($nomgroupe) {
        return $this->createQueryBuilder('g')
                        ->leftJoin('App\Entity\Groupe', 'e', Expr\Join::WITH, 'g.id = e.id')
//                        ->join('App\Entity\Cotisationgroupe', 'd', Join::WITH, 'g.cotisationgroupe = d.id')
                        ->andWhere('g.id = :val')
                        ->setParameter('val', $nomgroupe)
                        ->orderBy('g.id', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function findOneByCotisergroupe($id): ?Cotisergroupe {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
    
    
    public function getListeFideleParGroupe($user) {
        return $this->createQueryBuilder('g')
                        ->join('App\Entity\Groupe', 'e', Join::WITH, 'g.groupe = e.id')
                        ->leftJoin('App\Entity\User', 'u', Join::WITH, 'e.user = :u.id')
                        ->andWhere('e.id = :val')
                        ->andWhere('g.etatgroupe = 1')
                        ->setParameter('user', $user)
                        ->orderBy('g.id', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

}
