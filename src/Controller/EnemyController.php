<?php

namespace App\Controller;

use App\Entity\Enemy;
use App\Form\EnemyType;
use App\Repository\EnemyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
