<?php

namespace App\Repository;

use App\Entity\Heroe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Heroe>
 *
 * @method Heroe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Heroe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Heroe[]    findAll()
 * @method Heroe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeroeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Heroe::class);
    }

//    /**
//     * @return Heroe[] Returns an array of Heroe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Heroe
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
