<?php

namespace App\Controller;

use App\Entity\Heroe;
use App\Form\Heroe1Type;
use App\Repository\HeroeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
