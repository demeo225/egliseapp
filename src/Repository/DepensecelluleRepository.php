<?php

namespace App\Repository;

use App\Entity\Depensecellule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depensecellule>
 *
 * @method Depensecellule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depensecellule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depensecellule[]    findAll()
 * @method Depensecellule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepensecelluleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depensecellule::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Depensecellule $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Depensecellule $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Depensecellule[] Returns an array of Depensecellule objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Depensecellule
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

     /**
     * Recherche des dépenses avec filtres
     */
    public function rechercheDepenses(array $criteres, $dateDebut = null, $dateFin = null, $limit = 1000): array
    {
        $qb = $this->createQueryBuilder('d')
            ->where('d.eglise = :eglise')
            ->andWhere('d.deletedAt IS NULL')
            ->setParameter('eglise', $criteres['eglise']);
        
        if (isset($criteres['cellule']) && $criteres['cellule']) {
            $qb->andWhere('d.cellule = :cellule')
                ->setParameter('cellule', $criteres['cellule']);
        }
        
        if ($dateDebut) {
            $qb->andWhere('d.datedepense >= :dateDebut')
                ->setParameter('dateDebut', $dateDebut);
        }
        
        if ($dateFin) {
            $qb->andWhere('d.datedepense <= :dateFin')
                ->setParameter('dateFin', $dateFin);
        }
        
        return $qb->setMaxResults($limit)
            ->orderBy('d.datedepense', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
