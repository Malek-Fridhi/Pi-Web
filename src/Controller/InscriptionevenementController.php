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
    #[Route(name: 'app_inscriptionevenement_index', methods: ['GET'])]
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

  /*  #[Route('/{idinscriptionevenement}', name: 'app_inscriptionevenement_show', methods: ['GET'])]
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

    #[Route('/{idinscriptionevenement}', name: 'app_inscriptionevenement_delete', methods: ['POST'])]
    public function delete(Request $request, Inscriptionevenement $inscriptionevenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inscriptionevenement->getIdinscriptionevenement(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($inscriptionevenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_inscriptionevenement_index', [], Response::HTTP_SEE_OTHER);
    }*/
    #[Route('/{idinscriptionevenement}', name: 'app_inscriptionevenement_show', methods: ['GET'])]
    public function show(int $idinscriptionevenement, InscriptionevenementRepository $repository): Response
    {
        $inscriptionevenement = $repository->find($idinscriptionevenement);

        if (!$inscriptionevenement) {
            throw $this->createNotFoundException('Aucune inscription trouvée pour l\'ID ' . $idinscriptionevenement);
        }

        return $this->render('inscriptionevenement/show.html.twig', [
            'inscriptionevenement' => $inscriptionevenement,
        ]);
    }

    #[Route('/{idinscriptionevenement}/edit', name: 'app_inscriptionevenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $idinscriptionevenement, InscriptionevenementRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $inscriptionevenement = $repository->find($idinscriptionevenement);

        if (!$inscriptionevenement) {
            throw $this->createNotFoundException('Aucune inscription trouvée pour l\'ID ' . $idinscriptionevenement);
        }

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

    #[Route('/{idinscriptionevenement}/delete', name: 'app_inscriptionevenement_delete', methods: ['POST'])]
    public function delete(Request $request, int $idinscriptionevenement, InscriptionevenementRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $inscriptionevenement = $repository->find($idinscriptionevenement);

        if (!$inscriptionevenement) {
            throw $this->createNotFoundException('Aucune inscription trouvée pour l\'ID ' . $idinscriptionevenement);
        }

        if ($this->isCsrfTokenValid('delete' . $inscriptionevenement->getIdinscriptionevenement(), $request->request->get('_token'))) {
            $entityManager->remove($inscriptionevenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_inscriptionevenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
