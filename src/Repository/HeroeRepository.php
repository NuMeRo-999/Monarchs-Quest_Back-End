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


    /**
     * @return Heroe[] Returns an array of the last 3 Heroe objects
     */
    public function findLastThreeHeroes(): array
    {
        return $this->createQueryBuilder('h')
            ->orderBy('h.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function createHeroes(): array
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder
            ->select('h')
            ->from(Heroe::class, 'h')
            ->leftJoin('h.stages', 's')
            ->groupBy('h.id')
            ->having('COUNT(s) = 0');
            $queryBuilder->setMaxResults(1); // Editar si se quiere tener más heroes

        $query = $queryBuilder->getQuery();
        $heroes = $query->getResult();

        $generatedHeroes = [];
        foreach ($heroes as $heroType) {
            $hero = new Heroe();
            $hero->setHealthPoints($heroType->getHealthPoints());
            $hero->setMaxHealthPoints($heroType->getMaxHealthPoints());
            $hero->setAttackPower($heroType->getAttackPower());
            $hero->setDefense($heroType->getDefense());
            $hero->setCriticalStrikeChance($heroType->getCriticalStrikeChance());
            $hero->setLevel($heroType->getLevel());
            $hero->setExperience($heroType->getExperience());
            $hero->setState($heroType->getState()); // 1 Vivo - 0 Muerto
            $hero->addAbility($heroType->getAbilities()->first());
            $hero->setImageFilename($heroType->getImageFilename());
            $hero->setName($heroType->getName());

            $abilities = $heroType->getAbilities();
            foreach ($abilities as $ability) {
                $hero->addAbility($ability);
            }

            $generatedHeroes[] = $hero;
        }

        return $generatedHeroes;
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
