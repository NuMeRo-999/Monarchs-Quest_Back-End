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
    public function createRandomEnemies(int $count): array
    {
        // Buscar enemigos que no tengan un ID de etapa asignado
        $queryBuilder = $this->createQueryBuilder('e')
            ->where('e.stage IS NULL');

        $enemies = $queryBuilder->getQuery()->getResult();

        // Barajar aleatoriamente el array de enemigos
        shuffle($enemies);

        // Seleccionar el número especificado de enemigos (si hay menos, se tomarán todos)
        $enemies = array_slice($enemies, 0, $count);

        // Generar enemigos aleatorios basados en los tipos de enemigos disponibles
        $generatedEnemies = [];
        foreach ($enemies as $enemyType) {
            
            $enemy = new Enemy();
            $enemy->setName($enemyType->getName());
            $level = $enemyType->getLevel();

            $enemy->setHealthPoints(random_int(50 * $level, 100 * $level));
            $enemy->setAttackPower(random_int(10 * $level, 20 * $level));
            $enemy->setDefense(random_int(5 * $level, 15 * $level));
            $enemy->setCriticalStrikeChance(random_int(5 * $level, 15 * $level));
            $enemy->setLevel($level);
            $enemy->setState(1); // Vivo
            $enemy->setImageFilename($enemyType->getImageFilename());

            $generatedEnemies[] = $enemy;
        }

        return $generatedEnemies;
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
