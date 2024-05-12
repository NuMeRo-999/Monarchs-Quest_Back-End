<?php

namespace App\Controller;

use App\Entity\Stage;
use App\Form\StageType;
use App\Repository\StageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/stage')]
class StageController extends AbstractController
{

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'app_stage_index', methods: ['GET'])]
    public function index(StageRepository $stageRepository, SerializerInterface $serializer): Response
    {
        $stages = $stageRepository->findAll();

        $serializedStages = [];
        foreach ($stages as $stage) {
            $serializedStage = [
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
            $serializedStages[] = $serializedStage;
        }

        $data = $serializer->serialize($serializedStages, 'json');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/new', name: 'app_stage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stage = new Stage();
        $form = $this->createForm(StageType::class, $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stage);
            $entityManager->flush();

            return $this->redirectToRoute('app_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stage/new.html.twig', [
            'stage' => $stage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stage_show', methods: ['GET'])]
    public function show(Stage $stage): Response
    {
        return $this->render('stage/show.html.twig', [
            'stage' => $stage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stage $stage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StageType::class, $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stage/edit.html.twig', [
            'stage' => $stage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stage_delete', methods: ['POST'])]
    public function delete(Request $request, Stage $stage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $stage->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($stage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stage_index', [], Response::HTTP_SEE_OTHER);
    }
}
