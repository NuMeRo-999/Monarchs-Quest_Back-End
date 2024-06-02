<?php

namespace App\Repository;

use App\Entity\Enemy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Enemy>
 *
 * @method Enemy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enemy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enemy[]    findAll()
 * @method Enemy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnemyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enemy::class);
    }

    /**
     * Crea un número especificado de enemigos aleatorios que no tienen un ID de etapa asignado.
     *
     * @param int $count La cantidad de enemigos a crear.
     * @return Enemy[] Un array de enemigos aleatorios.
     */
    public function createRandomEnemies(int $count, int $stage): array
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->where('e.stage IS NULL');

        $enemies = $queryBuilder->getQuery()->getResult();

        shuffle($enemies);

        $enemies = array_slice($enemies, 0, $count);

        $generatedEnemies = [];
        foreach ($enemies as $enemyType) {
            $enemy = new Enemy();
            $enemy->setName($enemyType->getName());
            $level = $stage;

            $baseHealth = 100;
            $baseAttack = 20;
            $baseDefense = 10;
            $baseCrit = 5;

            $healthScalingFactor = $level > 1 ? 1.2 : 1;
            $attackScalingFactor = $level > 1 ? 1.1 : 1;
            $defenseScalingFactor = $level > 1 ? 1.1 : 1;
            $critScalingFactor = $level > 1 ? 1.05 : 1;

            $healthPoints = $baseHealth * pow($healthScalingFactor, $level - 1);
            $attackPower = $baseAttack * pow($attackScalingFactor, $level - 1);
            $defense = $baseDefense * pow($defenseScalingFactor, $level - 1);
            $criticalStrikeChance = $baseCrit * pow($critScalingFactor, $level - 1);

            $enemy->setHealthPoints(random_int($healthPoints * 0.9, $healthPoints * 1.1));
            $enemy->setAttackPower(random_int($attackPower * 0.9, $attackPower * 1.1));
            $enemy->setDefense(random_int($defense * 0.9, $defense * 1.1));
            $enemy->setCriticalStrikeChance(random_int($criticalStrikeChance * 0.9, $criticalStrikeChance * 1.1));
            $enemy->setLevel($level);
            $enemy->setState(1); // Vivo
            $enemy->setImageFilename($enemyType->getImageFilename());

            $generatedEnemies[] = $enemy;
        }

        return $generatedEnemies;
    }


    /**
     * Elimina todos los enemigos con estado 0 de una etapa específica.
     *
     * @param int $stageId El ID de la etapa.
     */
    public function deleteEnemiesByStage(int $stageId): void
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->delete()
            ->where('e.stage = :stageId')
            ->andWhere('e.state = 0')
            ->setParameter('stageId', $stageId);

        $queryBuilder->getQuery()->execute();
    }
    //    /**
    //     * @return Enemy[] Returns an array of Enemy objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Enemy
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
