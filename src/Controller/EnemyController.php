<?php

namespace App\Controller;

use App\Entity\Enemy;
use App\Entity\Heroe;
use App\Form\EnemyType;
use App\Repository\EnemyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/enemy')]
class EnemyController extends AbstractController
{
    #[Route('/', name: 'app_enemy_index', methods: ['GET'])]
    public function index(EnemyRepository $enemyRepository): Response
    {
        return $this->render('enemy/index.html.twig', [
            'enemies' => $enemyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_enemy_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $enemy = new Enemy();
        $form = $this->createForm(EnemyType::class, $enemy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imgFile = $form->get('image')->getData();
    
                if ($imgFile) {
                    $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
    
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imgFile->guessExtension();
                }
                try {
                    $imgFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) { }
    
                $enemy->setImageFilename($newFilename);

            $entityManager->persist($enemy);
            $entityManager->flush();

            return $this->redirectToRoute('app_enemy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enemy/new.html.twig', [
            'enemy' => $enemy,
            'form' => $form,
        ]);
    }

    #[Route('/attack/{heroe}', name: 'app_enemy_attack', methods: ['GET'])]
    public function enemyAttack(Heroe $heroe, EntityManagerInterface $entityManager): Response
    {
        $enemies = $heroe->getStages()->toArray()[0]->getEnemies()->toArray();
        $damage = 0;

        foreach ($enemies as $enemy) {
            $damage += $enemy->getAttackPower() - $heroe->getDefense();
            $randomNumber = mt_rand(1, 100);
            if ($randomNumber <= $enemy->getCriticalStrikeChance()) {
                $damage *= 2;
            }
        }

        if ($damage < 0) {
            $damage = 0;
        }

        $heroe->setHealthPoints($heroe->getHealthPoints() - $damage);

        if ($heroe->getHealthPoints() <= 0) {
            $heroe->setHealthPoints(0);
            $heroe->setState(false);
        }

        $heroe->getStages()->toArray()[0]->setState(1);
        
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

    #[Route('/{id}', name: 'app_enemy_show', methods: ['GET'])]
    public function show(Enemy $enemy): Response
    {
        return $this->render('enemy/show.html.twig', [
            'enemy' => $enemy,
        ]);
    }
    

    #[Route('/{id}/edit', name: 'app_enemy_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enemy $enemy, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EnemyType::class, $enemy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_enemy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enemy/edit.html.twig', [
            'enemy' => $enemy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_enemy_delete', methods: ['POST'])]
    public function delete(Request $request, Enemy $enemy, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enemy->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($enemy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_enemy_index', [], Response::HTTP_SEE_OTHER);
    }
}
