<?php

namespace App\Repository;

use App\Entity\Infection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Infection|null find($id, $lockMode = null, $lockVersion = null)
 * @method Infection|null findOneBy(array $criteria, array $orderBy = null)
 * @method Infection[]    findAll()
 * @method Infection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Infection::class);
    }

    // /**
    //  * @return Infection[] Returns an array of Infection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Infection
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}