<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllAsArray(array $properties = [])
    {


        $selectStr = 'u';
        if (count($properties) > 0) {
            $fun = function ($field) {
                return 'u.' . $field;
            };
            $_props = array_map($fun, $properties);
            $selectStr = implode(', ', $_props);
        }
        $query = new Query($this->_em);

        $query->setDQL("SELECT $selectStr FROM App\Entity\User u
            WHERE u.confirmationToken is null
            ORDER BY u.id DESC");

//        return [$query->getDQL(), $query->getSQL()];
         return $query->getResult();

//            ->setMaxResults(10)
//            ->setHydrationMode(AbstractQuery::HYDRATE_ARRAY)
//            ->getResult()
//            ;
    }

    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
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
