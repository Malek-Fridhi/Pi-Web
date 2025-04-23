<?php

namespace App\Controller;

use App\Entity\Inscriptionevenement;
use App\Form\InscriptionevenementType;
use App\Repository\InscriptionevenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inscriptionevenement')]
final class InscriptionevenementController extends AbstractController
{
    #[Route('/', name: 'app_inscriptionevenement_index', methods: ['GET'])]
    public function index(InscriptionevenementRepository $inscriptionevenementRepository): Response
    {
        return $this->render('inscriptionevenement/index.html.twig', [
            'inscriptionevenements' => $inscriptionevenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_inscriptionevenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $inscriptionevenement = new Inscriptionevenement();
        $form = $this->createForm(InscriptionevenementType::class, $inscriptionevenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($inscriptionevenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_inscriptionevenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inscriptionevenement/new.html.twig', [
            'inscriptionevenement' => $inscriptionevenement,
            'form' => $form,
        ]);
    }

    #[Route('/{idinscriptionevenement}', name: 'app_inscriptionevenement_show', methods: ['GET'])]
    public function show(Inscriptionevenement $inscriptionevenement): Response
    {
        return $this->render('inscriptionevenement/show.html.twig', [
            'inscriptionevenement' => $inscriptionevenement,
        ]);
    }

    #[Route('/{idinscriptionevenement}/edit', name: 'app_inscriptionevenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inscriptionevenement $inscriptionevenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InscriptionevenementType::class, $inscriptionevenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_inscriptionevenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inscriptionevenement/edit.html.twig', [
            'inscriptionevenement' => $inscriptionevenement,
            'form' => $form,
        ]);
    }

    #[Route('/{idinscriptionevenement}/accept', name: 'app_inscriptionevenement_accept', methods: ['POST'])]
    public function accept(
        Request $request, 
        Inscriptionevenement $inscriptionevenement, 
        EntityManagerInterface $entityManager,
        InscriptionevenementRepository $repository
    ): Response {
        // Vérifier la capacité de l'événement
        $event = $inscriptionevenement->getIdevenement();
        $acceptedCount = $repository->count(['idevenement' => $event, 'statut' => 'Approved']);
        
        if ($acceptedCount >= $event->getCapacite()) {
            $this->addFlash('error', 'La capacité maximale de l\'événement est atteinte.');
            return $this->redirectToRoute('app_evenement_inscriptions', ['idevenement' => $event->getIdevenement()]);
        }

        if ($this->isCsrfTokenValid('accept'.$inscriptionevenement->getIdinscriptionevenement(), $request->request->get('_token'))) {
            $inscriptionevenement->setStatut('Approved');
            $entityManager->flush();
            $this->addFlash('success', 'Inscription acceptée avec succès.');
        }

        return $this->redirectToRoute('app_evenement_inscriptions', ['idevenement' => $event->getIdevenement()]);
    }

    #[Route('/{idinscriptionevenement}/reject', name: 'app_inscriptionevenement_reject', methods: ['POST'])]
    public function reject(
        Request $request, 
        Inscriptionevenement $inscriptionevenement, 
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('reject'.$inscriptionevenement->getIdinscriptionevenement(), $request->request->get('_token'))) {
            $inscriptionevenement->setStatut('Canceled');
            $entityManager->flush();
            $this->addFlash('success', 'Inscription refusée avec succès.');
        }

        return $this->redirectToRoute('app_evenement_inscriptions', ['idevenement' => $inscriptionevenement->getIdevenement()->getIdevenement()]);
    }

    #[Route('/{idinscriptionevenement}', name: 'app_inscriptionevenement_delete', methods: ['POST'])]
    public function delete(Request $request, Inscriptionevenement $inscriptionevenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inscriptionevenement->getIdinscriptionevenement(), $request->request->get('_token'))) {
            $entityManager->remove($inscriptionevenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_inscriptionevenement_index', [], Response::HTTP_SEE_OTHER);
    }
}