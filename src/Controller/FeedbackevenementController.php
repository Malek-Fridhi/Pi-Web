<?php

namespace App\Controller;

use App\Entity\Feedbackevenement;
use App\Form\FeedbackevenementType;
use App\Repository\FeedbackevenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/feedbackevenement')]
final class FeedbackevenementController extends AbstractController
{
    #[Route(name: 'app_feedbackevenement_index', methods: ['GET'])]
    public function index(FeedbackevenementRepository $feedbackevenementRepository): Response
    {
        return $this->render('feedbackevenement/index.html.twig', [
            'feedbackevenements' => $feedbackevenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_feedbackevenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feedbackevenement = new Feedbackevenement();
        $form = $this->createForm(FeedbackevenementType::class, $feedbackevenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($feedbackevenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_feedbackevenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('feedbackevenement/new.html.twig', [
            'feedbackevenement' => $feedbackevenement,
            'form' => $form,
        ]);
    }

    /*#[Route('/{idfeedbackevenement}', name: 'app_feedbackevenement_show', methods: ['GET'])]
    public function show(Feedbackevenement $feedbackevenement): Response
    {
        return $this->render('feedbackevenement/show.html.twig', [
            'feedbackevenement' => $feedbackevenement,
        ]);
    }

    #[Route('/{idfeedbackevenement}/edit', name: 'app_feedbackevenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feedbackevenement $feedbackevenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FeedbackevenementType::class, $feedbackevenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_feedbackevenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('feedbackevenement/edit.html.twig', [
            'feedbackevenement' => $feedbackevenement,
            'form' => $form,
        ]);
    }

    #[Route('/{idfeedbackevenement}', name: 'app_feedbackevenement_delete', methods: ['POST'])]
    public function delete(Request $request, Feedbackevenement $feedbackevenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feedbackevenement->getIdfeedbackevenement(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($feedbackevenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_feedbackevenement_index', [], Response::HTTP_SEE_OTHER);
    }*/
    #[Route('/{idfeedbackevenement}', name: 'app_feedbackevenement_show', methods: ['GET'])]
    public function show(FeedbackevenementRepository $feedbackevenementRepository, int $idfeedbackevenement): Response
    {
        $feedbackevenement = $feedbackevenementRepository->find($idfeedbackevenement);

        if (!$feedbackevenement) {
            throw $this->createNotFoundException('Feedback non trouvé.');
        }

        return $this->render('feedbackevenement/show.html.twig', [
            'feedbackevenement' => $feedbackevenement,
        ]);
    }

    // ✅ Modifier un feedback avec `idfeedbackevenement`
    #[Route('/{idfeedbackevenement}/edit', name: 'app_feedbackevenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FeedbackevenementRepository $feedbackevenementRepository, int $idfeedbackevenement, EntityManagerInterface $entityManager): Response
    {
        $feedbackevenement = $feedbackevenementRepository->find($idfeedbackevenement);

        if (!$feedbackevenement) {
            throw $this->createNotFoundException('Feedback non trouvé.');
        }

        $form = $this->createForm(FeedbackevenementType::class, $feedbackevenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_feedbackevenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('feedbackevenement/edit.html.twig', [
            'feedbackevenement' => $feedbackevenement,
            'form' => $form->createView(),
        ]);
    }

    // ✅ Supprimer un feedback avec `idfeedbackevenement`
    #[Route('/{idfeedbackevenement}', name: 'app_feedbackevenement_delete', methods: ['POST'])]
    public function delete(Request $request, FeedbackevenementRepository $feedbackevenementRepository, int $idfeedbackevenement, EntityManagerInterface $entityManager): Response
    {
        $feedbackevenement = $feedbackevenementRepository->find($idfeedbackevenement);

        if (!$feedbackevenement) {
            throw $this->createNotFoundException('Feedback non trouvé.');
        }

        if ($this->isCsrfTokenValid('delete' . $feedbackevenement->getIdfeedbackevenement(), $request->request->get('_token'))) {
            $entityManager->remove($feedbackevenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_feedbackevenement_index', [], Response::HTTP_SEE_OTHER);
    }

}
