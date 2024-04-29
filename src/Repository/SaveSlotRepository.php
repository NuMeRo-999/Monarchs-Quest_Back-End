<?php

namespace App\Repository;

use App\Entity\SaveSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SaveSlot>
 *
 * @method SaveSlot|null find($id, $lockMode = null, $lockVersion = null)
 * @method SaveSlot|null findOneBy(array $criteria, array $orderBy = null)
 * @method SaveSlot[]    findAll()
 * @method SaveSlot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaveSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaveSlot::class);
    }

//    /**
//     * @return SaveSlot[] Returns an array of SaveSlot objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SaveSlot
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
