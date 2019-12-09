<?php

namespace App\Repository;

use App\Entity\SocImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SocImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocImage[]    findAll()
 * @method SocImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocImage::class);
    }

    // /**
    //  * @return SocImage[] Returns an array of SocImage objects
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
    public function findOneBySomeField($value): ?SocImage
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
