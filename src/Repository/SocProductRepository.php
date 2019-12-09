<?php

namespace App\Repository;

use App\Entity\SocProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SocProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocProduct[]    findAll()
 * @method SocProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocProduct::class);
    }

    // /**
    //  * @return SocProduct[] Returns an array of SocProduct objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SocProduct
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
