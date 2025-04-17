<?php

namespace App\Controller;

use App\Entity\Cour;
use App\Form\CourType;
use App\Repository\CourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/CourBack')]
final class CourControllerBack extends AbstractController
{
    #[Route(name: 'app_cour_index_back', methods: ['GET'])]
    public function index(CourRepository $courRepository): Response
    {
        return $this->render('cour/indexBack.html.twig', [
            'cours' => $courRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cour_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, CourRepository $courRepository): Response
{
    $cour = new Cour();
    $form = $this->createForm(CourType::class, $cour);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if (!$form->isValid()) {
            // Display the form with validation errors
            return $this->render('cour/new.html.twig', [
                'cour' => $cour,
                'form' => $form->createView(),
            ]);
        }

        // Duplicate check only if form is valid
        $existingCour = $courRepository->findOneBy([
            'nom' => $cour->getNom(),
            'description' => $cour->getDescription(),
            'capacite' => $cour->getCapacite()
        ]);

        if ($existingCour) {
            $this->addFlash('error', 'Ce cours existe déjà !');
            return $this->render('cour/new.html.twig', [
                'cour' => $cour,
                'form' => $form->createView(),
            ]);
        }

        $entityManager->persist($cour);
        $entityManager->flush();

        return $this->redirectToRoute('app_cour_index_back', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('cour/new.html.twig', [
        'cour' => $cour,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id}', name: 'app_cour_show', methods: ['GET'])]
    public function show(Cour $cour): Response
    {
        return $this->render('cour/show.html.twig', [
            'cour' => $cour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cour $cour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CourType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cour_index_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cour/edit.html.twig', [
            'cour' => $cour,
            'form' => $form->createView()

        ]);
    }

    #[Route('/{id}', name: 'app_cour_delete', methods: ['POST'])]
    public function delete(Request $request, Cour $cour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cour_index_back', [], Response::HTTP_SEE_OTHER);
    }
}
