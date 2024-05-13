<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $users = $userRepository->findAll();

        $serializedUsers = [];
        foreach ($users as $user) {
            $serializedUser = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
                'game' => $user->getGame(),
            ];
            $serializedUsers[] = $serializedUser;
        }

        $data = $serializer->serialize($serializedUsers, 'json');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $serializedUser = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'game' => $user->getGame(),
        ];

        $data = $this->serializer->serialize($serializedUser, 'json');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/save-slots', name: 'app_user_save_slots', methods: ['GET'])]
    public function getSaveSlots(User $user): Response
    {
        $saveSlots = $user->getGame()->getSaveSlot();

        $serializedSaveSlots = [];
        foreach ($saveSlots as $saveSlot) {
            $serializedSaveSlot = [
                'id' => $saveSlot->getId(),
                'creationDate' => $saveSlot->getCreationDate(),
                'money' => $saveSlot->getMoney(),
                'kills' => $saveSlot->getKills(),
                'stage' => array_map(function ($stage) {
                    return [
                        'id' => $stage->getId(),
                        'stage' => $stage->getStage(),
                    ];
                }, $saveSlot->getStage()->toArray()),
            ];
            $serializedSaveSlots[] = $serializedSaveSlot;
        }

        $data = $this->serializer->serialize($serializedSaveSlots, 'json');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }
}
