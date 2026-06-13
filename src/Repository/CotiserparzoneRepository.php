<?php

namespace App\Repository;

use App\Entity\Cotiserparzone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cotiserparzone>
 *
 * @method Cotiserparzone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cotiserparzone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cotiserparzone[]    findAll()
 * @method Cotiserparzone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotiserparzoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotiserparzone::class);
    }

//    /**
//     * @return Cotiserparzone[] Returns an array of Cotiserparzone objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cotiserparzone
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
