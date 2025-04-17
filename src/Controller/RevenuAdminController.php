<?php

namespace App\Controller;

use App\Entity\Revenu;
use App\Form\RevenuType;
use App\Repository\RevenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/revenuadmin')]
final class RevenuAdminController extends AbstractController
{
    #[Route(name: 'app_revenu_indexadmin', methods: ['GET'])]
    public function index(RevenuRepository $revenuRepository): Response
    {
        return $this->render('revenu/indexadmin.html.twig', [
            'revenus' => $revenuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_revenu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $revenu = new Revenu();
        $form = $this->createForm(RevenuType::class, $revenu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($revenu);
            $entityManager->flush();

            return $this->redirectToRoute('app_revenu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('revenu/new.html.twig', [
            'revenu' => $revenu,
            'form' => $form,
        ]);
    }

/*    #[Route('/{idrevenu}', name: 'app_revenu_show', methods: ['GET'])]
    public function show(Revenu $revenu): Response
    {
        return $this->render('revenu/show.html.twig', [
            'revenu' => $revenu,
        ]);
    }

    #[Route('/{idrevenu}/edit', name: 'app_revenu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Revenu $revenu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RevenuType::class, $revenu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_revenu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('revenu/edit.html.twig', [
            'revenu' => $revenu,
            'form' => $form,
        ]);
    }

    #[Route('/{idrevenu}', name: 'app_revenu_delete', methods: ['POST'])]
    public function delete(Request $request, Revenu $revenu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$revenu->getIdrevenu(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($revenu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_revenu_index', [], Response::HTTP_SEE_OTHER);
    }*/
    #[Route('/{idrevenu}', name: 'app_revenu_show', methods: ['GET'])]
    public function show(int $idrevenu, RevenuRepository $repository): Response
    {
        $revenu = $repository->find($idrevenu);

        if (!$revenu) {
            throw $this->createNotFoundException('Aucun revenu trouvé pour l\'ID ' . $idrevenu);
        }

        return $this->render('revenu/show.html.twig', [
            'revenu' => $revenu,
        ]);
    }

    #[Route('/{idrevenu}/edit', name: 'app_revenu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $idrevenu, RevenuRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $revenu = $repository->find($idrevenu);

        if (!$revenu) {
            throw $this->createNotFoundException('Aucun revenu trouvé pour l\'ID ' . $idrevenu);
        }

        $form = $this->createForm(RevenuType::class, $revenu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_revenu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('revenu/edit.html.twig', [
            'revenu' => $revenu,
            'form' => $form,
        ]);
    }

    #[Route('/{idrevenu}/delete', name: 'app_revenu_delete', methods: ['POST'])]
    public function delete(Request $request, int $idrevenu, RevenuRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $revenu = $repository->find($idrevenu);

        if (!$revenu) {
            throw $this->createNotFoundException('Aucun revenu trouvé pour l\'ID ' . $idrevenu);
        }

        if ($this->isCsrfTokenValid('delete' . $revenu->getIdrevenu(), $request->request->get('_token'))) {
            $entityManager->remove($revenu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_revenu_index', [], Response::HTTP_SEE_OTHER);
    }


}
