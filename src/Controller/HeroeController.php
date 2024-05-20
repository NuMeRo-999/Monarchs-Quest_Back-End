<?php

namespace App\Controller;

use App\Entity\Enemy;
use App\Entity\Heroe;
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
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, EnemyController $enemyController): Response
    {
        $heroe = new Heroe();
        $form = $this->createForm(Heroe1Type::class, $heroe);
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
    public function buff(Heroe $heroe, Skill $skill, EntityManager $entityManager )
    {
        // preguntar si filtrar por if o hacerlo así
        $heroe->setDefense($heroe->getDefense() + $skill->getDefense());
        $heroe->setHealthPoints($heroe->getHealthPoints() + $skill->getHealthPoints());
        $heroe->setMaxHealthPoints($heroe->getMaxHealthPoints() + $skill->getHealthPoints());
        $heroe->setAttackPower($heroe->getAttackPower() + $skill->getAttackDamage());
        $heroe->setCriticalStrikeChance($heroe->getCriticalStrikeChance() + $skill->getCriticalStrikeChance());
        
        $entityManager->flush();
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

        $criticalStrikeChance = $heroe->getCriticalStrikeChance() + $weaponCriticalStrikeChance + $amulet[0]->getCriticalStrikeChance() + $skill->getCriticalStrikeChance();
        $damage = $heroe->getAttackPower() + $weaponDamage + $amulet[0]->getAttackPower() + $skill->getAttackDamage();

        $randomNumber = mt_rand(1, 100);

        if ($randomNumber <= $criticalStrikeChance) {
            $damage *= 2;
            dd('Critical Strike! Damage: ' . $damage);
        }

        $enemy->setHealthPoints($enemy->getHealthPoints() - $damage);

        if($enemy->getHealthPoints() <= 0) {
            $saveSlot->setKills($saveSlot->getKills() + 1);
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

        return new JsonResponse([
            'message' => 'Attack successful',
            'enemies' => $enemiesData,
        ], Response::HTTP_OK);

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
        if ($this->isCsrfTokenValid('delete'.$heroe->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($heroe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_heroe_index', [], Response::HTTP_SEE_OTHER);
    }
}
