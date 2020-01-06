<?php

namespace App\Repository;

use App\Entity\TranslatedDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TranslatedDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method TranslatedDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method TranslatedDocument[]    findAll()
 * @method TranslatedDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranslatedDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TranslatedDocument::class);
    }

    // /**
    //  * @return TranslatedDocument[] Returns an array of TranslatedDocument objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TranslatedDocument
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
