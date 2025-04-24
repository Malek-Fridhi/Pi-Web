<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Inscriptionevenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Repository\InscriptionevenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/evenement')]
#[IsGranted('ROLE_ADMIN')]
final class EvenementControllerBack extends AbstractController
{
    #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{idevenement}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(int $idevenement, EvenementRepository $evenementRepository): Response
    {
        $evenement = $evenementRepository->find($idevenement);
        
        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{idevenement}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        int $idevenement,
        EvenementRepository $evenementRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $evenement = $evenementRepository->find($idevenement);
        
        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{idevenement}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        int $idevenement,
        EvenementRepository $evenementRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $evenement = $evenementRepository->find($idevenement);
        
        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        if ($this->isCsrfTokenValid('delete'.$evenement->getIdevenement(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{idevenement}/inscriptions', name: 'app_evenement_inscriptions', methods: ['GET'])]
    public function showInscriptions(
        int $idevenement,
        EvenementRepository $evenementRepository,
        InscriptionevenementRepository $inscriptionRepo
    ): Response {
        $evenement = $evenementRepository->find($idevenement);
        
        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        $inscriptions = $inscriptionRepo->findBy(['evenement' => $evenement], ['date_inscription' => 'DESC']);

        $approvedCount = $inscriptionRepo->count([
            'evenement' => $evenement,
            'statut' => 'Approved'
        ]);

        return $this->render('evenement/inscriptions.html.twig', [
            'evenement' => $evenement,
            'inscriptions' => $inscriptions,
            'approved_count' => $approvedCount,
            'capacity_reached' => $evenement->getCapacite() && $approvedCount >= $evenement->getCapacite()
        ]);
    }

    #[Route('/inscription/{id}/approve', name: 'app_inscription_approve', methods: ['POST'])]
    public function approveInscription(
        int $id,
        InscriptionevenementRepository $inscriptionRepo,
        EntityManagerInterface $entityManager
    ): Response {
        $inscription = $inscriptionRepo->find($id);
        
        if (!$inscription) {
            throw $this->createNotFoundException('Inscription non trouvée');
        }

        $evenement = $inscription->getEvenement();
        
        // Check capacity
        $approvedCount = $inscriptionRepo->count([
            'evenement' => $evenement,
            'statut' => 'Approved'
        ]);

        if ($evenement->getCapacite() && $approvedCount >= $evenement->getCapacite()) {
            $this->addFlash('danger', 'La capacité maximale est atteinte pour cet événement');
            return $this->redirectToRoute('app_evenement_inscriptions', [
                'idevenement' => $evenement->getIdevenement()
            ]);
        }

        $inscription->setStatut('Approved');
        $entityManager->flush();

        $this->addFlash('success', 'Inscription approuvée avec succès');
        return $this->redirectToRoute('app_evenement_inscriptions', [
            'idevenement' => $evenement->getIdevenement()
        ]);
    }

    #[Route('/inscription/{id}/reject', name: 'app_inscription_reject', methods: ['POST'])]
    public function rejectInscription(
        int $id,
        InscriptionevenementRepository $inscriptionRepo,
        EntityManagerInterface $entityManager
    ): Response {
        $inscription = $inscriptionRepo->find($id);
        
        if (!$inscription) {
            throw $this->createNotFoundException('Inscription non trouvée');
        }

        $inscription->setStatut('Canceled');
        $entityManager->flush();

        $this->addFlash('warning', 'Inscription rejetée');
        return $this->redirectToRoute('app_evenement_inscriptions', [
            'idevenement' => $inscription->getEvenement()->getIdevenement()
        ]);
    }
}