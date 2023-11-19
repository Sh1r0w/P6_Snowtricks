<?php

namespace App\Repository;

use App\Entity\Connect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Connect>
 *
 * @method Connect|null find($id, $lockMode = null, $lockVersion = null)
 * @method Connect|null findOneBy(array $criteria, array $orderBy = null)
 * @method Connect[]    findAll()
 * @method Connect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConnectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Connect::class);
    }

//    /**
//     * @return Connect[] Returns an array of Connect objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Connect
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
