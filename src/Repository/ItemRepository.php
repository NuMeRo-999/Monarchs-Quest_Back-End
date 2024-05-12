<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\SaveSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 *
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function createItems(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();


        $qb->select('i')
        ->from(Item::class, 'i')
        ->leftJoin('i.saveSlots', 'ss')
        ->groupBy('i.id')
        ->having('COUNT(ss) = 0');

        $query = $qb->getQuery();

        $items = $query->getResult();

        $generatedItems = [];
        foreach ($items as $itemType) {

            // dd($itemType);
            $item = new Item();
            $item->setName($itemType->getName());
            $item->setDescription($itemType->getDescription());
            $item->setCriticalStrikeChance($itemType->getCriticalStrikeChance());
            $item->setAttackPower($itemType->getAttackPower());
            $item->setDefense($itemType->getDefense());
            $item->setQuantity($itemType->getQuantity());
            $item->setRarity($itemType->getRarity());
            $item->setType($itemType->getType());
            $item->setImageFilename($itemType->getImageFilename());
            $item->setState($itemType->getState());

            $generatedItems[] = $item;
        }

        return $generatedItems;
    }

    //    /**
    //     * @return Item[] Returns an array of Item objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Item
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
