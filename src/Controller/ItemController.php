<?php

namespace App\Controller;

use App\Entity\Item;
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

    #[Route('/equiped_weapons', name: 'app_weapon_item_equiped', methods: ['GET'])]
    public function getEquippedWeapons(ItemRepository $itemRepository): Response
    {
        $items = $itemRepository->getWeaponsEquiped();

        $serializedItems = [];

        foreach ($items as $item) {
            $serializedItems[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'image' => $item->getImageFilename(),
                'attackPower' => $item->getAttackPower(),
                'criticalStrikeChance' => $item->getCriticalStrikeChance(),
                'defense' => $item->getDefense(),
                'state' => $item->getState(),
            ];
        }

        $data = $this->serializer->serialize($serializedItems, 'json');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/equiped_amulet', name: 'app_amulet_item_equiped', methods: ['GET'])]
    public function getEquippedAmulet(ItemRepository $itemRepository): Response
    {
        $items = $itemRepository->getAmuletEquiped();

        $serializedItems = [];

        foreach ($items as $item) {
            $serializedItems[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'image' => $item->getImageFilename(),
                'attackPower' => $item->getAttackPower(),
                'criticalStrikeChance' => $item->getCriticalStrikeChance(),
                'defense' => $item->getDefense(),
                'state' => $item->getState(),
            ];
        }

        $data = $this->serializer->serialize($serializedItems, 'json');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/equiped_items', name: 'app_item_equiped', methods: ['GET'])]
    public function getEquippedItems(ItemRepository $itemRepository): Response
    {
        $items = $itemRepository->getItemAtInventory();

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
