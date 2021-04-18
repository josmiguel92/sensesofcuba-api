<?php

namespace App\Repository;

use App\Entity\UsersGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsersGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsersGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsersGroup[]    findAll()
 * @method UsersGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsersGroup::class);
    }

    // /**
    //  * @return UsersGroup[] Returns an array of UsersGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UsersGroup
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
