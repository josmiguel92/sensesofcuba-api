<?php

namespace App\Repository;

use App\Entity\SocProduct;
use App\Entity\TranslatedDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;

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

    public function findByEnabledOrdered()
    {

        $query = new Query($this->_em);
        $query->setDQL('SELECT product, parent, image, transDoc, deFile, enFile, esFile, ProductTranslation

            FROM App\Entity\SocProduct product
            LEFT JOIN product.parent parent WITH product.parent = parent.id
            LEFT JOIN product.image image WITH product.image = image.id

            LEFT JOIN product.translatedDocument transDoc WITH product.translatedDocument = transDoc.id
            LEFT JOIN transDoc.deFile deFile WITH transDoc.deFile =  deFile.id
            LEFT JOIN transDoc.enFile enFile WITH transDoc.enFile =  enFile.id
            LEFT JOIN transDoc.esFile esFile WITH transDoc.esFile =  esFile.id

            LEFT JOIN App\Entity\SocProductTranslation ProductTranslation WITH ProductTranslation.translatable =  product.id

            WHERE product.enabled = true
            ORDER BY product.importance DESC');

         return $query->getResult();

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
