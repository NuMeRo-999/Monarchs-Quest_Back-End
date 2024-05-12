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

        // Serializar los objetos SaveSlot excluyendo las propiedades problemáticas
        $serializedSaveSlots = [];
        foreach ($saveSlots as $saveSlot) {
            $serializedSaveSlot = [
                'id' => $saveSlot->getId(),
                'creationDate' => $saveSlot->getCreationDate(),
                'money' => $saveSlot->getMoney(),
                'game' => $saveSlot->getGame(),
                'stage' => $saveSlot->getStage(),
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

        dd($saveSlot);

        // Stage
        $stage = new Stage();
        $stage->setStage(1);
        $heroes = $heroeRepository->createHeroes();
        foreach ($heroes as $hero) {
            $stage->addHero($hero);
            $entityManager->persist($hero);
        }

        $enemies = $enemyRepository->createRandomEnemies(3);
        foreach ($enemies as $enemy) {
            $stage->addEnemy($enemy);
            $entityManager->persist($enemy);
        }
        
        $saveSlot->addStage($stage);

        $form = $this->createForm(SaveSlotType::class, $saveSlot);
        $form->submit($data);

        if ($form->isValid()) {
            $entityManager->persist($saveSlot);
            $entityManager->persist($stage);
            $entityManager->flush();

            return $this->json($saveSlot, 201);
        }

        return $this->json(['error' => 'Invalid data'], 400);
    }

    #[Route('/{id}', name: 'app_save_slot_show', methods: ['GET'])]
    public function show(SaveSlot $saveSlot): Response
    {

        $serializedSaveSlot = [
            'id' => $saveSlot->getId(),
            'creationDate' => $saveSlot->getCreationDate(),
            'money' => $saveSlot->getMoney(),
            'game' => $saveSlot->getGame(),
            'stage' => $saveSlot->getStage(),
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

    #[Route('/{id}', name: 'app_save_slot_delete', methods: ['DELETE'])]
    public function delete(Request $request, SaveSlot $saveSlot, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($saveSlot);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
