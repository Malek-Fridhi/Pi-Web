<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reclamation')]
final class ReclamationController extends AbstractController
{
    #[Route(name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }

/*    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }*/

/*    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $reclamation = new Reclamation();
    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Set the current user if needed
        // $reclamation->setUser($this->getUser());
        
        // Set current date if needed
        // $reclamation->setDate(new \DateTime());
        
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réclamation a été enregistrée avec succès!');
        return $this->redirectToRoute('app_reclamation_index');
    }

    return $this->render('reclamation/new.html.twig', [
        'form' => $form->createView(),
    ]);
}*/


#[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $reclamation = new Reclamation();
    
    // Set the current user
    $user = $this->getUser();
    if (!$user) {
        // Handle case where user is not logged in
        $this->addFlash('error', 'Vous devez être connecté pour créer une réclamation.');
        return $this->redirectToRoute('app_login');
    }
    $reclamation->setUser($user);
    
    // Set current date automatically
    $reclamation->setDate(new \DateTime());
    
    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réclamation a été enregistrée avec succès!');
        return $this->redirectToRoute('app_reclamation_index');
    }

    return $this->render('reclamation/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/statistiques', name: 'app_reclamation_stats')]
    public function statistiques(ReclamationRepository $reclamationRepository): Response
    {
        $total = $reclamationRepository->countTotalReclamations();
        $byStatus = $reclamationRepository->countByStatus();
        $byMonth = $reclamationRepository->countByMonth();

        return $this->render('reclamation/stats.html.twig', [
            'total' => $total,
            'byStatus' => $byStatus,
            'byMonth' => $byMonth,
        ]);
    }


    #[Route('/reclamation', name: 'app_reclamation_indexx', methods: ['GET'])]
public function indexx(Request $request, ReclamationRepository $reclamationRepository): Response
{
    $query = $request->query->get('q');
    $status = $request->query->get('status');
    $sort = $request->query->get('sort', 'date'); // Tri par défaut sur la date
    $direction = $request->query->get('direction', 'asc'); // Sens par défaut

    $reclamations = $reclamationRepository->searchWithSorting($query, $status, $sort, $direction);

    return $this->render('reclamation/index.html.twig', [
        'reclamations' => $reclamations,
        'sort' => $sort,
        'direction' => $direction
    ]);
}

}
