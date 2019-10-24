<?php

namespace App\Repository;

use App\Entity\Subcategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Subcategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subcategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subcategory[]    findAll()
 * @method Subcategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubcategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subcategory::class);
    }

    /**
     * @return Subcategory[] Returns an array of Subcategory objects
     */
    public function findByCategory($cat)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.category = :cat')
            ->setParameter('cat', $cat)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.users', 'u')
            ->where('u.id = :user')
            ->setParameter('user', $user['user'])
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUserAndCategory($user, $cat)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.users', 'u')
            ->where('u.id = :user')
            ->andWhere('s.category = :cat')
            ->setParameter('user', $user)
            ->setParameter('cat', $cat)
            ->getQuery()
            ->getResult()
        ;
    }
    /*
    public function findOneBySomeField($value): ?Subcategory
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
