<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'app_game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findAll();
        
        $gamesData = [];

        foreach ($games as $game) {
            $gamesData[] = [
                'id' => $game->getId(),
                'user' => array_map(function ($user) {
                    return [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'roles' => $user->getRoles(),
                    ];
                }, $game->getUser()->toArray()),
                'saveSlot' => array_map(function ($saveSlot) {
                    return [
                        'id' => $saveSlot->getId(),
                        'creationDate' => $saveSlot->getCreationDate(),
                        'money' => $saveSlot->getMoney(),
                        'stage' => array_map(function ($stage) {
                            return [
                                'id' => $stage->getId(),
                                'stage' => $stage->getStage(),
                            ];
                        }, $saveSlot->getStage()->toArray()),
                    ];
                }, $game->getSaveSlot()->toArray()),
            ];
        }

        $data = json_encode($gamesData);

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/new', name: 'app_game_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->submit($data);

        if ($form->isValid()) {
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->json($game, 201);
        }

        return $this->json(['error' => 'Invalid data'], 400);
    }

    #[Route('/{id}', name: 'app_game_show', methods: ['GET'])]
    public function show(Game $game): Response
    {
        return $this->json($game, 200);
    }

    #[Route('/{id}/edit', name: 'app_game_edit', methods: ['PUT'])]
    public function edit(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(GameType::class, $game);
        $form->submit($data);

        if ($form->isValid()) {
            $entityManager->flush();

            return $this->json($game, 200);
        }

        return $this->json(['error' => 'Invalid data'], 400);
    }

    #[Route('/{id}', name: 'app_game_delete', methods: ['DELETE'])]
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($game);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
