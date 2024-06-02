<?php

namespace App\Controller;

use App\Entity\Heroe;
use App\Entity\Item;
use App\Entity\SaveSlot;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/item')]
class ItemController extends AbstractController
{

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'app_item_index', methods: ['GET'])]
    public function index(ItemRepository $itemRepository): Response
    {
        return $this->render('item/index.html.twig', [
            'items' => $itemRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
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

            $item->setImageFilename($newFilename);

            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute('app_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('item/new.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/equiped_weapons/{save}', name: 'app_weapon_item_equiped', methods: ['GET'])]
    public function getEquippedWeapons(SaveSlot $save, ItemRepository $itemRepository): Response
    {
        $hero = $save->getStage()->toArray()[0]->getHeroes()->toArray()[0];
        $items = $itemRepository->getWeaponsEquiped($hero->getId());

        $serializedItems = [];

        foreach ($items as $item) {
            $serializedItems[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'image' => $item->getImageFilename(),
                'attackPower' => $item->getAttackPower(),
                'criticalStrikeChance' => $item->getCriticalStrikeChance(),
                'healthPoints' => $item->getHealthPoints(),
                'defense' => $item->getDefense(),
                'state' => $item->getState(),
            ];
        }

        $data = $this->serializer->serialize($serializedItems, 'json');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/equiped_amulet/{save}', name: 'app_amulet_item_equiped', methods: ['GET'])]
    public function getEquippedAmulet(SaveSlot $save, ItemRepository $itemRepository): Response
    {
        $hero = $save->getStage()->toArray()[0]->getHeroes()->toArray()[0];
        $items = $itemRepository->getAmuletEquiped($hero->getId());

        $serializedItems = [];

        foreach ($items as $item) {
            $serializedItems[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'image' => $item->getImageFilename(),
                'attackPower' => $item->getAttackPower(),
                'criticalStrikeChance' => $item->getCriticalStrikeChance(),
                'healthPoints' => $item->getHealthPoints(),
                'defense' => $item->getDefense(),
                'state' => $item->getState(),
            ];
        }

        $data = $this->serializer->serialize($serializedItems, 'json');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/equiped_items/{save}', name: 'app_item_equiped', methods: ['GET'])]
    public function getEquippedItems(SaveSlot $save, ItemRepository $itemRepository): Response
    {
        $hero = $save->getStage()->toArray()[0]->getHeroes()->toArray()[0];
        $items = $itemRepository->getItemAtInventory($hero->getId());

        $serializedItems = [];

        foreach ($items as $item) {
            $serializedItems[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'quantity' => $item->getQuantity(),
                'image' => $item->getImageFilename(),
                'attackPower' => $item->getAttackPower(),
                'criticalStrikeChance' => $item->getCriticalStrikeChance(),
                'healthPoints' => $item->getHealthPoints(),
                'maxHealthPoints' => $item->getMaxHealthPoints(),
                'defense' => $item->getDefense(),
                'type' => $item->getType(),
                'state' => $item->getState(),
            ];
        }

        $data = $this->serializer->serialize($serializedItems, 'json');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/{id}', name: 'app_item_show', methods: ['GET'])]
    public function show(Item $item): Response
    {
        $serializedItem = [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'description' => $item->getDescription(),
            'image' => $item->getImageFilename(),
            'attackPower' => $item->getAttackPower(),
            'criticalStrikeChance' => $item->getCriticalStrikeChance(),
            'defense' => $item->getDefense(),
            'healthPoints' => $item->getHealthPoints(),
            'maxHealthPoints' => $item->getMaxHealthPoints(),
            'state' => $item->getState(),
        ];

        $data = $this->serializer->serialize($serializedItem, 'json');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Item $item, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('item/edit.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_item_delete', methods: ['POST'])]
    public function delete(Request $request, Item $item, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
