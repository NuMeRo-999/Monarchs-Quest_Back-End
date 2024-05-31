<?php

namespace App\Controller;

use App\Entity\Enemy;
use App\Entity\Heroe;
use App\Entity\Item;
use App\Entity\Skill;
use App\Form\Heroe1Type;
use App\Repository\HeroeRepository;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/heroe')]
class HeroeController extends AbstractController
{
    #[Route('/', name: 'app_heroe_index', methods: ['GET'])]
    public function index(HeroeRepository $heroeRepository): Response
    {
        return $this->render('heroe/index.html.twig', [
            'heroes' => $heroeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_heroe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $heroe = new Heroe();
        $form = $this->createForm(Heroe1Type::class, $heroe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imgFile = $form->get('image')->getData();

            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imgFile->guessExtension();
            }
            try {
                $imgFile->move(
                    $this->getParameter('image_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
            }

            $heroe->setImageFilename($newFilename);

            $entityManager->persist($heroe);
            $entityManager->flush();

            return $this->redirectToRoute('app_heroe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('heroe/new.html.twig', [
            'heroe' => $heroe,
            'form' => $form,
        ]);
    }

    #[Route('/buff/{heroe}/{skill}', name: 'app_heroe_buff', methods: ['GET'])]
    public function buff(Heroe $heroe, Skill $skill, EntityManagerInterface $entityManager)
    {
        $heroe->setDefense($heroe->getDefense() + $skill->getDefense());
        $heroe->setHealthPoints($heroe->getHealthPoints() + $skill->getHealthPoints());
        $heroe->setMaxHealthPoints($heroe->getMaxHealthPoints() + $skill->getHealthPoints());
        $heroe->setAttackPower($heroe->getAttackPower() + $skill->getAttackDamage());
        $heroe->setCriticalStrikeChance($heroe->getCriticalStrikeChance() + $skill->getCriticalStrikeChance());

        $stage = $heroe->getStages()->toArray()[0];
        $stage->setState(2);

        $entityManager->flush();

        $saveSlot = $heroe->getStages()->toArray()[0]->getSaveSlot();


        $saveSlotData = [
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

        $heroeData = [
            'id' => $heroe->getId(),
            'level' => $heroe->getLevel(),
            'experience' => $heroe->getExperience(),
            'name' => $heroe->getName(),
            'healthPoints' => $heroe->getHealthPoints(),
            'maxHealthPoints' => $heroe->getMaxHealthPoints(),
            'attackPower' => $heroe->getAttackPower(),
            'defense' => $heroe->getDefense(),
            'criticalStrikeChance' => $heroe->getCriticalStrikeChance(),
            'imageFilename' => $heroe->getImageFilename(),
        ];

        return new JsonResponse([
            'heroe' => $heroeData,
            'saveSlot' => $saveSlotData
        ], Response::HTTP_OK);
    }

    #[Route('/attack/{heroe}/{enemy}/{skill}', name: 'app_heroe_attack', methods: ['GET'])]
    public function attack(Heroe $heroe, Enemy $enemy, Skill $skill, EntityManagerInterface $entityManager, ItemRepository $itemRepository)
    {

        $weapon = $itemRepository->getWeaponsEquiped($heroe->getId());
        $amulet = $itemRepository->getAmuletEquiped($heroe->getId());
        $saveSlot = $heroe->getStages()->toArray()[0]->getSaveSlot();

        $weaponDamage = 0;
        $weaponCriticalStrikeChance = 0;
        foreach ($weapon as $w) {
            $weaponDamage += $w->getAttackPower();
            $weaponCriticalStrikeChance += $w->getCriticalStrikeChance();
        }

        $criticalStrikeChance = $heroe->getCriticalStrikeChance() + $weaponCriticalStrikeChance + ($amulet ? $amulet[0]->getCriticalStrikeChance() : 0) + $skill->getCriticalStrikeChance();
        $damage = $heroe->getAttackPower() + $weaponDamage + ($amulet ? $amulet[0]->getAttackPower() : 0) + $skill->getAttackDamage();

        $randomNumber = mt_rand(1, 100);

        if ($randomNumber <= $criticalStrikeChance) {
            $damage *= 2;
            dd('Critical Strike! Damage: ' . $damage);
        }

        $enemy->setHealthPoints($enemy->getHealthPoints() - $damage);
        $stage = $heroe->getStages()->toArray()[0];
        $stage->setState(2);

        if ($enemy->getHealthPoints() <= 0) {
            $saveSlot->setKills($saveSlot->getKills() + 1);
            $heroe->setExperience($heroe->getExperience() + (20 * $enemy->getLevel()));
            $saveSlot->setMoney($saveSlot->getMoney() + (5 * $enemy->getLevel()));
            $enemy->setState(false);
            $enemy->setHealthPoints(0);
        }

        $entityManager->flush();

        $enemies = $heroe->getStages()->toArray()[0]->getEnemies()->toArray();

        $enemiesData = [];
        foreach ($enemies as $enemy) {
            $enemiesData[] = [
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
        }

        // Check if all enemies have state 0
        $allEnemiesDefeated = true;
        foreach ($enemies as $enemy) {
            if ($enemy->getState() != 0) {
                $allEnemiesDefeated = false;
                break;
            }
        }

        if ($allEnemiesDefeated) {
            $stage->setState(0);


            $entityManager->flush();
        }

        $saveSlotData = [
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

        return new JsonResponse([
            'enemies' => $enemiesData,
            'saveSlot' => $saveSlotData,
        ], Response::HTTP_OK);
    }

    #[Route('/consume-item/{heroe}/{item}', name: 'app_heroe_consume_item', methods: ['POST'])]
    public function consumeItem(Heroe $heroe, Item $item, EntityManagerInterface $entityManager): Response
    {
        $heroe->setHealthPoints($heroe->getHealthPoints() + $item->getHealthPoints());
        $heroe->setMaxHealthPoints($heroe->getMaxHealthPoints() + $item->getMaxHealthPoints());
        $heroe->setAttackPower($heroe->getAttackPower() + $item->getAttackPower());
        $heroe->setDefense($heroe->getDefense() + $item->getDefense());
        $heroe->setCriticalStrikeChance($heroe->getCriticalStrikeChance() + $item->getCriticalStrikeChance());

        if ($item->getQuantity() > 1) {
            $item->setQuantity($item->getQuantity() - 1);
        } else {
            $heroe->removeWeapon1($item);
        }

        $entityManager->flush();

        return new JsonResponse([], Response::HTTP_OK);
    }

    #[Route('/equip-item/{item}', name: 'app_heroe_equip_weapon', methods: ['POST'])]
    public function equipWeapon(Item $item, EntityManagerInterface $entityManager): Response
    {
        $item->setState(true);
        $entityManager->flush();

        return new JsonResponse([], Response::HTTP_OK);
    }

    #[Route('/unequip-item/{item}', name: 'app_heroe_unequip_item', methods: ['POST'])]
    public function unequipItem(Item $item, EntityManagerInterface $entityManager): Response
    {
        $item->setState(false);
        $entityManager->flush();

        return new JsonResponse([], Response::HTTP_OK);
    }

    #[Route('/delete-item/{heroe}/{item}', name: 'app_heroe_delete_item', methods: ['POST'])]
    public function deleteItem(Heroe $heroe, Item $item, EntityManagerInterface $entityManager): Response
    {
        $heroe->removeWeapon1($item);
        $entityManager->flush();

        return new JsonResponse([], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_heroe_show', methods: ['GET'])]
    public function show(Heroe $heroe): Response
    {
        return $this->render('heroe/show.html.twig', [
            'heroe' => $heroe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_heroe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Heroe $heroe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Heroe1Type::class, $heroe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_heroe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('heroe/edit.html.twig', [
            'heroe' => $heroe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_heroe_delete', methods: ['POST'])]
    public function delete(Request $request, Heroe $heroe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $heroe->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($heroe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_heroe_index', [], Response::HTTP_SEE_OTHER);
    }
}
