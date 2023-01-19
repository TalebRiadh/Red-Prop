<?php

namespace App\Repository;

use App\Entity\Orderlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Orderlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orderlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orderlist[]    findAll()
 * @method Orderlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderlistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orderlist::class);
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    
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
