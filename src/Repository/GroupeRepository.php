<?php

namespace App\Repository;

use App\Entity\Groupe;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Groupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Groupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Groupe[]    findAll()
 * @method Groupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Groupe::class);
    }

    // /**
    //  * @return Groupe[] Returns an array of Groupe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Groupe
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
        
   
        public function findOneGroupe($id): ?Groupe {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

          public function findOneByUser(User $user): ?Groupe
        {
            return $this->createQueryBuilder('z')
                ->innerJoin('z.users', 'u')
                ->where('u = :user')
                ->andWhere('z.deletedAt IS NULL')
                ->setParameter('user', $user)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }


         public function findAccessibleByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('g');
        
        if ($user->getIddepartement()) {
            $qb->andWhere('g.iddepartement = :deptId')
               ->setParameter('deptId', $user->getIddepartement());
        } else {
            return [];
        }
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Récupère toutes les séances des groupes accessibles
     */
    public function findAllSeancesAccessibles(User $user): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s')
           ->from('App\Entity\Seancegroupe', 's')
           ->join('s.idgroupe', 'g')
           ->where('g.iddepartement = :deptId')
           ->setParameter('deptId', $user->getIddepartement());
        
        return $qb->getQuery()->getResult();
    }
    

}
