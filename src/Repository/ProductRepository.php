<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

     /**
    * @return Product[] Returns an array of Product objects
    */
    
    public function findAllDisableProduct($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.state = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult()
        ;
    }
     public function findAll()
    {
        return $this->findBy(array(), array('id' => 'DESC'));
    }

    public function findAllDispoProduct()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.state = :val')
            ->setParameter('val', true)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    public function AltSameProduct($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
    

    
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
     public function findOneByCode($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.code = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }





    public function findSearchProduct(string $search): ?Product
    {
        $query = $this->createQueryBuilder('p');
        if($search){
            $query = $query->andwhere(' p.name like :product')
                ->setParameter('product', $search.'%');
        }


        return $query->getQuery()->getOneOrNullResult();
    }


}
