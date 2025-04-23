<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/evenement')]
final class EvenementControllerBack extends AbstractController
{
    #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

/*************  ✨ Windsurf Command ⭐  *************/
/**
 * Create a new Evenement entity.
 *
 * This method handles the creation of a new Evenement. It displays a form
 * for creating an Evenement and processes the form submission. If the form
 * is submitted and valid, the new Evenement is persisted to the database
 * and the user is redirected to the event index page.
 *
 * @param Request $request The HTTP request object.
 * @param EntityManagerInterface $entityManager The entity manager for database operations.
 *
 * @return Response The HTTP response rendering the form or redirecting.
 */

/*******  6b0f6d02-6ada-43a7-ac85-17a63721e092  *******/
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

  /*  #[Route('/{idevenement}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{idevenement}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
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
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getIdevenement(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }*/
    #[Route('/{idevenement}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(EvenementRepository $evenementRepository, int $idevenement): Response
    {
        $evenement = $evenementRepository->find($idevenement);

        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé.');
        }

        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }
    // Ajoutez cette méthode dans votre EvenementController
#[Route('/{idevenement}/inscriptions', name: 'app_evenement_inscriptions', methods: ['GET'])]
public function showInscriptions(
    Evenement $evenement,
    InscriptionevenementRepository $inscriptionRepository
): Response {
    $inscriptions = $inscriptionRepository->findBy(['idevenement' => $evenement]);

    return $this->render('evenement/inscriptions.html.twig', [
        'evenement' => $evenement,
        'inscriptions' => $inscriptions,
    ]);
}

    // ✅ Modifier un événement avec `idevenement`
   // src/Controller/EvenementController.php
#[Route('/{idevenement}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, EvenementRepository $evenementRepository, int $idevenement, EntityManagerInterface $entityManager): Response
{
    $evenement = $evenementRepository->find($idevenement);

    if (!$evenement) {
        throw $this->createNotFoundException('Événement non trouvé.');
    }

    $form = $this->createForm(EvenementType::class, $evenement);
    $form->handleRequest($request);

    // Création du formulaire de suppression
    $deleteForm = $this->createFormBuilder()
        ->setAction($this->generateUrl('app_evenement_delete', ['idevenement' => $idevenement]))
        ->setMethod('POST')
        ->getForm();

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('evenement/edit.html.twig', [
        'evenement' => $evenement,
        'form' => $form->createView(),
        'delete_form' => $deleteForm->createView(),
    ]);
}

    // ✅ Supprimer un événement avec `idevenement`
    #[Route('/{idevenement}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, EvenementRepository $evenementRepository, int $idevenement, EntityManagerInterface $entityManager): Response
    {
        $evenement = $evenementRepository->find($idevenement);

        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé.');
        }

        if ($this->isCsrfTokenValid('delete' . $evenement->getIdevenement(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
