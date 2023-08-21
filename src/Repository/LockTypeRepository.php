<?php

namespace App\Repository;

use App\Entity\LockType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LockType>
 *
 * @method LockType|null find($id, $lockMode = null, $lockVersion = null)
 * @method LockType|null findOneBy(array $criteria, array $orderBy = null)
 * @method LockType[]    findAll()
 * @method LockType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LockTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LockType::class);
    }

//    /**
//     * @return LockType[] Returns an array of LockType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LockType
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
