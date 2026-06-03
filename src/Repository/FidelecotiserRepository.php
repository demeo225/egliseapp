<?php

namespace App\Repository;

use App\Entity\Fidelecotiser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fidelecotiser|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fidelecotiser|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fidelecotiser[]    findAll()
 * @method Fidelecotiser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FidelecotiserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fidelecotiser::class);
    }

    // /**
    //  * @return Fidelecotiser[] Returns an array of Fidelecotiser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fidelecotiser
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
    
    public function findOneByFidelecotiser($id): ?Fidelecotiser {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
}
