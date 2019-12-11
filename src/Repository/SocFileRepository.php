<?php

namespace App\Repository;

use App\Entity\SocFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SocFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocFile[]    findAll()
 * @method SocFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocFile::class);
    }

    // /**
    //  * @return SocFile[] Returns an array of SocFile objects
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
    public function findOneBySomeField($value): ?SocFile
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
