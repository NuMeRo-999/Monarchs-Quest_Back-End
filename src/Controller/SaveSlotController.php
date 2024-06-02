<?php

namespace App\Controller;

use App\Entity\SaveSlot;
use App\Entity\Stage;
use App\Entity\User;
use App\Form\SaveSlotType;
use App\Repository\EnemyRepository;
use App\Repository\HeroeRepository;
use App\Repository\ItemRepository;
use App\Repository\SaveSlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/save/slot')]
class SaveSlotController extends AbstractController
{

    private $serializer;

    public function __construct(SerializerInterface $serializer, SecurityBundleSecurity $security)
    {
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'app_save_slot_index', methods: ['GET'])]
    public function index(SaveSlotRepository $saveSlotRepository): Response
    {
        $saveSlots = $saveSlotRepository->findAll();

        $serializedSaveSlots = [];
        foreach ($saveSlots as $saveSlot) {
            $serializedSaveSlot = [
                'id' => $saveSlot->getId(),
                'creationDate' => $saveSlot->getCreationDate(),
                'money' => $saveSlot->getMoney(),
                'kills' => $saveSlot->getKills(),
                'game' => $saveSlot->getGame()->getId(),
                'user' => array_map(function ($user) {
                    return [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                    ];
                }, $saveSlot->getGame()->getUser()->toArray()),
                'stage' => array_map(function ($stage) {
                    return [
                        'id' => $stage->getId(),
                        'stage' => $stage->getStage(),
                        'heroes' => array_map(function ($hero) {
                            return [
                                'id' => $hero->getId(),
                                'healthPoints' => $hero->getHealthPoints(),
                                'attackPower' => $hero->getAttackPower(),
                                'criticalStrikeChance' => $hero->getCriticalStrikeChance(),
                                'defense' => $hero->getDefense(),
                                'experience' => $hero->getExperience(),
                                'level' => $hero->getLevel(),
                                'state' => $hero->getState(),
                                'maxHealthPoints' => $hero->getMaxHealthPoints(),
                                'imageFilename' => $hero->getImageFilename(),
                                'name' => $hero->getName(),
                            ];
                        }, $stage->getHeroes()->toArray()),
                        'enemies' => array_map(function ($enemy) {
                            return [
                                'id' => $enemy->getId(),
                                'healthPoints' => $enemy->getHealthPoints(),
                                'attackPower' => $enemy->getAttackPower(),
                                'defense' => $enemy->getDefense(),
                                'criticalStrikeChance' => $enemy->getCriticalStrikeChance(),
                                'level' => $enemy->getLevel(),
                                'state' => $enemy->getState(),
                                'name' => $enemy->getName(),
                                'imageFilename' => $enemy->getImageFilename(),
                            ];
                        }, $stage->getEnemies()->toArray())
                    ];
                }, $saveSlot->getStage()->toArray()),
            ];
            $serializedSaveSlots[] = $serializedSaveSlot;
        }

        return $this->json($serializedSaveSlots, 200);
    }

    #[Route('/new', name: 'app_save_slot_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $saveSlot = new SaveSlot();
        $form = $this->createForm(SaveSlotType::class, $saveSlot);
        $form->submit($data);

        if ($form->isValid()) {
            $entityManager->persist($saveSlot);
            $entityManager->flush();

            return $this->json($saveSlot, 201);
        }

        return $this->json(['error' => 'Invalid data'], 400);
    }

    #[Route('/create/{user}', name: 'app_save_slot_create', methods: ['POST'])]
    public function createSaveSlot(Request $request, User $user, EntityManagerInterface $entityManager, HeroeRepository $heroeRepository, EnemyRepository $enemyRepository, ItemRepository $itemRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $game = $user->getGame();

        // SaveSLot
        $saveSlot = new SaveSlot();
        $saveSlot->setCreationDate(new \DateTime());
        $saveSlot->setMoney(0);
        $saveSlot->setKills(0);
        $saveSlot->setState(0);

        $items = $itemRepository->createItems();
        foreach ($items as $item) {
            $saveSlot->addInventario($item);
            $entityManager->persist($item);
        }
        $saveSlot->setGame($game);

        // Stage
        $stage = new Stage();
        $stage->setStage(1);
        $stage->setState(1);

        $heroes = $heroeRepository->createHeroes();
        foreach ($heroes as $hero) {
            $stage->addHero($hero);
            $entityManager->persist($hero);
        }

        $enemies = $enemyRepository->createRandomEnemies(3, $stage->getStage());
        foreach ($enemies as $enemy) {
            $stage->addEnemy($enemy);
            $entityManager->persist($enemy);
        }

        $saveSlot->addStage($stage);

        $entityManager->persist($saveSlot);
        $entityManager->persist($stage);
        $entityManager->flush();

        $json = $this->serializer->serialize($saveSlot, 'json', [
            'groups' => 'saveSlot_serialization',
        ]);

        return new JsonResponse($json, 201, [], true);
    }

    #[Route('/add-items/{id}', name: 'app_save_slot_add_items', methods: ['GET'])]
    public function addItems(SaveSlot $saveSlot, ItemRepository $itemRepository, EntityManagerInterface $entityManager): Response
    {
        $moneyToAdd = mt_rand(20, 50) * $saveSlot->getStage()[0]->getStage();
        $saveSlot->setMoney($saveSlot->getMoney() + $moneyToAdd);

        $items = $itemRepository->getItemsAtInventory($saveSlot);
        $maxItemsToAdd = 3;
        $itemsAdded = 0;

        $serializedItems = [];
        foreach ($items as $item) {
            $rarity = $item->getRarity();

            $probability = 0;
            if ($rarity === 'común') {
                $probability = 0.5;
            } elseif ($rarity === 'raro') {
                $probability = 0.3;
            } elseif ($rarity === 'épico') {
                $probability = 0.2;
            }

            if (mt_rand() / mt_getrandmax() < $probability) {
                $hero = $saveSlot->getStage()[0]->getHeroes()[0];
                $existingItem = null;

                foreach ($hero->getWeapon1() as $inventoryItem) {
                    if ($inventoryItem->getId() === $item->getId()) {
                        $existingItem = $inventoryItem;
                        break;
                    }
                }

                if ($existingItem) {
                    if ($item->getType() === 'consumible') {
                        $existingItem->setQuantity($existingItem->getQuantity() + 1);
                        $itemsAdded++;
                        $serializedItem = [
                            'id' => $item->getId(),
                            'name' => $item->getName(),
                            'description' => $item->getDescription(),
                            'type' => $item->getType(),
                            'defense' => $item->getDefense(),
                            'quantity' => $item->getQuantity(),
                            'attackPower' => $item->getAttackPower(),
                            'healthPoints' => $item->getHealthPoints(),
                            'criticalStrikeChance' => $item->getCriticalStrikeChance(),
                            'rarity' => $item->getRarity(),
                            'imageFilename' => $item->getImageFilename(),
                        ];
                        $serializedItems[] = $serializedItem;
                    }
                } else {
                    if ($item->getType() === 'consumible') {
                        $hero->addWeapon1($item);
                        $itemsAdded++;
                    } else {
                        $hero->addWeapon1($item);
                        $itemsAdded++;
                    }
                    $entityManager->persist($item);

                    $serializedItem = [
                        'id' => $item->getId(),
                        'name' => $item->getName(),
                        'description' => $item->getDescription(),
                        'type' => $item->getType(),
                        'defense' => $item->getDefense(),
                        'quantity' => $item->getQuantity(),
                        'attackPower' => $item->getAttackPower(),
                        'healthPoints' => $item->getHealthPoints(),
                        'criticalStrikeChance' => $item->getCriticalStrikeChance(),
                        'rarity' => $item->getRarity(),
                        'imageFilename' => $item->getImageFilename(),
                    ];
                    $serializedItems[] = $serializedItem;

                    if ($itemsAdded >= $maxItemsToAdd) {
                        break;
                    }
                }
            }
        }

        // Si no se añadió ningún ítem, añadir uno al azar
        if ($itemsAdded === 0 && count($items) > 0) {
            $consumableItems = array_filter($items, function ($item) {
                return $item->getType() === 'consumible';
            });

            if (count($consumableItems) > 0) {
                $randomItem = $consumableItems[array_rand($consumableItems)];
            } else {
                $randomItem = $items[array_rand($items)];
            }

            $hero = $saveSlot->getStage()[0]->getHeroes()[0];
            $hero->addWeapon1($randomItem);
            $entityManager->persist($randomItem);

            $serializedItem = [
                'id' => $randomItem->getId(),
                'name' => $randomItem->getName(),
                'description' => $randomItem->getDescription(),
                'type' => $randomItem->getType(),
                'defense' => $randomItem->getDefense(),
                'quantity' => $randomItem->getQuantity(),
                'attackPower' => $randomItem->getAttackPower(),
                'healthPoints' => $randomItem->getHealthPoints(),
                'criticalStrikeChance' => $randomItem->getCriticalStrikeChance(),
                'rarity' => $randomItem->getRarity(),
                'imageFilename' => $randomItem->getImageFilename(),
            ];
            $serializedItems[] = $serializedItem;
        }

        $entityManager->flush();

        return $this->json($serializedItems, 200);
    }

    #[Route('/next-stage/{id}', name: 'app_save_slot_next_stage', methods: ['GET'])]
    public function nextStage(SaveSlot $saveSlot, EntityManagerInterface $entityManager, EnemyRepository $enemyRepository): Response
    {
        $stage = $saveSlot->getStage()[0];
        $stage->setState(1);
        $stage->setStage($stage->getStage() + 1);

        $enemyRepository->deleteEnemiesByStage($stage->getId());

        $enemies = $enemyRepository->createRandomEnemies(3, $stage->getStage());
        foreach ($enemies as $enemy) {
            $stage->addEnemy($enemy);
            $entityManager->persist($enemy);
        }

        $entityManager->flush();

        $serializedSaveSlot = [
            'id' => $saveSlot->getId(),
            'creationDate' => $saveSlot->getCreationDate(),
            'money' => $saveSlot->getMoney(),
            'kills' => $saveSlot->getKills(),
            'game' => $saveSlot->getGame()->getId(),
            'stage' => array_map(function ($stage) {
                return [
                    'id' => $stage->getId(),
                    'stage' => $stage->getStage(),
                    'state' => $stage->getState(),
                    'heroes' => array_map(function ($hero) {
                        return [
                            'id' => $hero->getId(),
                            'healthPoints' => $hero->getHealthPoints(),
                            'attackPower' => $hero->getAttackPower(),
                            'criticalStrikeChance' => $hero->getCriticalStrikeChance(),
                            'defense' => $hero->getDefense(),
                            'experience' => $hero->getExperience(),
                            'level' => $hero->getLevel(),
                            'state' => $hero->getState(),
                            'maxHealthPoints' => $hero->getMaxHealthPoints(),
                            'imageFilename' => $hero->getImageFilename(),
                            'name' => $hero->getName(),
                            'abilities' => array_map(function ($ability) {
                                return [
                                    'id' => $ability->getId(),
                                    'name' => $ability->getName(),
                                    'description' => $ability->getDescription(),
                                    'attack_damage' => $ability->getAttackDamage(),
                                    'critical_strike_chance' => $ability->getCriticalStrikeChance(),
                                    'defense' => $ability->getDefense(),
                                    'health_points' => $ability->getHealthPoints(),
                                    'type' => $ability->getType(),
                                    'imageFilename' => $ability->getImageFilename(),
                                ];
                            }, $hero->getAbilities()->toArray()),
                        ];
                    }, $stage->getHeroes()->toArray()),
                    'enemies' => array_map(function ($enemy) {
                        return [
                            'id' => $enemy->getId(),
                            'healthPoints' => $enemy->getHealthPoints(),
                            'attackPower' => $enemy->getAttackPower(),
                            'defense' => $enemy->getDefense(),
                            'criticalStrikeChance' => $enemy->getCriticalStrikeChance(),
                            'level' => $enemy->getLevel(),
                            'state' => $enemy->getState(),
                            'name' => $enemy->getName(),
                            'imageFilename' => $enemy->getImageFilename(),
                        ];
                    }, $stage->getEnemies()->toArray())
                ];
            }, $saveSlot->getStage()->toArray()),
        ];

        return $this->json($serializedSaveSlot, 200);
    }

    #[Route('/{id}', name: 'app_save_slot_show', methods: ['GET'])]
    public function show(SaveSlot $saveSlot): Response
    {

        $serializedSaveSlot = [
            'id' => $saveSlot->getId(),
            'creationDate' => $saveSlot->getCreationDate(),
            'money' => $saveSlot->getMoney(),
            'kills' => $saveSlot->getKills(),
            'game' => $saveSlot->getGame()->getId(),
            'stage' => array_map(function ($stage) {
                return [
                    'id' => $stage->getId(),
                    'stage' => $stage->getStage(),
                    'state' => $stage->getState(),
                    'heroes' => array_map(function ($hero) {
                        return [
                            'id' => $hero->getId(),
                            'healthPoints' => $hero->getHealthPoints(),
                            'attackPower' => $hero->getAttackPower(),
                            'criticalStrikeChance' => $hero->getCriticalStrikeChance(),
                            'defense' => $hero->getDefense(),
                            'experience' => $hero->getExperience(),
                            'level' => $hero->getLevel(),
                            'state' => $hero->getState(),
                            'maxHealthPoints' => $hero->getMaxHealthPoints(),
                            'imageFilename' => $hero->getImageFilename(),
                            'name' => $hero->getName(),
                            'abilities' => array_map(function ($ability) {
                                return [
                                    'id' => $ability->getId(),
                                    'name' => $ability->getName(),
                                    'description' => $ability->getDescription(),
                                    'attack_damage' => $ability->getAttackDamage(),
                                    'critical_strike_chance' => $ability->getCriticalStrikeChance(),
                                    'defense' => $ability->getDefense(),
                                    'health_points' => $ability->getHealthPoints(),
                                    'type' => $ability->getType(),
                                    'imageFilename' => $ability->getImageFilename(),
                                ];
                            }, $hero->getAbilities()->toArray()),
                        ];
                    }, $stage->getHeroes()->toArray()),
                    'enemies' => array_map(function ($enemy) {
                        return [
                            'id' => $enemy->getId(),
                            'healthPoints' => $enemy->getHealthPoints(),
                            'attackPower' => $enemy->getAttackPower(),
                            'defense' => $enemy->getDefense(),
                            'criticalStrikeChance' => $enemy->getCriticalStrikeChance(),
                            'level' => $enemy->getLevel(),
                            'state' => $enemy->getState(),
                            'name' => $enemy->getName(),
                            'imageFilename' => $enemy->getImageFilename(),
                        ];
                    }, $stage->getEnemies()->toArray())
                ];
            }, $saveSlot->getStage()->toArray()),
        ];

        $data = $this->serializer->serialize($serializedSaveSlot, 'json');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_save_slot_edit', methods: ['PUT'])]
    public function edit(Request $request, SaveSlot $saveSlot, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(SaveSlotType::class, $saveSlot);
        $form->submit($data);

        if ($form->isValid()) {
            $entityManager->flush();

            return $this->json($saveSlot, 200);
        }

        return $this->json(['error' => 'Invalid data'], 400);
    }

    #[Route('/end-state/{id}', name: 'app_save_slot_reset_state', methods: ['GET'])]
    public function resetState(SaveSlot $saveSlot, EntityManagerInterface $entityManager): Response
    {
        $saveSlot->setState(1);
        $entityManager->flush();

        return $this->json([], 200);
    }

    #[Route('/{id}', name: 'app_save_slot_delete', methods: ['DELETE'])]
    public function delete(SaveSlot $saveSlot, EntityManagerInterface $entityManager): Response
    {
        // Elimino todos los items y heroes relacionados
        foreach ($saveSlot->getStage() as $stage) {
            foreach ($stage->getHeroes() as $hero) {
                $entityManager->remove($hero);
            }
            $entityManager->remove($stage);
        }

        foreach ($saveSlot->getInventario() as $item) {
            $entityManager->remove($item);
        }

        $entityManager->remove($saveSlot);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
