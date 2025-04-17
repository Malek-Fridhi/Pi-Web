<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\File;
use Dompdf\Dompdf;
use Dompdf\Options;


#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /*#[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }*/
    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $produit = new Produit();
    $form = $this->createForm(ProduitType::class, $produit);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('image')->getData();
        
        if ($imageFile) {
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            
            // Déplacer le fichier
            $imageFile->move(
                $this->getParameter('produits_directory'), // Assurez-vous d'avoir ce paramètre configuré dans services.yaml
                $newFilename
            );
            
            // Mettre à jour l'entité
            $produit->setImage($newFilename);
        }

        $entityManager->persist($produit);
        $entityManager->flush();

        return $this->redirectToRoute('app_produit_index');
    }

    return $this->render('produit/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

 /*   #[Route('/{id_produit}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id_produit}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id_produit}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId_produit(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }*/
    #[Route('/export-pdf', name: 'app_produit_export_pdf')]
public function exportToPdf(ProduitRepository $produitRepository): Response
{
    $produits = $produitRepository->findAll();
    
    if (empty($produits)) {
        $this->addFlash('warning', 'Aucun produit à exporter');
        return $this->redirectToRoute('app_produit_index');
    }

    $html = $this->renderView('produit/export_pdf.html.twig', [
        'produits' => $produits,
        'date' => new \DateTime()
    ]);
    
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    return new Response(
        $dompdf->output(),
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="liste_produits_'.date('Y-m-d_H-i').'.pdf"'
        ]
    );
}
    #[Route('/{id_produit}', name: 'app_produit_show', methods: ['GET'])]
    public function show(int $id_produit, ProduitRepository $repository): Response
    {
        $produit = $repository->find($id_produit);
    
        if (!$produit) {
            throw $this->createNotFoundException('Aucun produit trouvé pour l\'ID ' . $id_produit);
        }
    
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id_produit}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
public function edit(
    Request $request,
    int $id_produit,
    ProduitRepository $repository,
    EntityManagerInterface $entityManager
): Response {
    // Récupération du produit
    $produit = $repository->find($id_produit);
    if (!$produit) {
        throw $this->createNotFoundException('Produit non trouvé');
    }

    // Sauvegarde de l'ancien nom d'image
    $ancienneImage = $produit->getImage();
    $uploadDir = $this->getParameter('produits_directory');

    // Création du formulaire
    $form = $this->createForm(ProduitType::class, $produit, [
        'image_required' => false
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('image')->getData();
        
        // Gestion de l'image
        if ($imageFile) {
            // Suppression de l'ancienne image
            if ($ancienneImage && file_exists($uploadDir.'/'.$ancienneImage)) {
                unlink($uploadDir.'/'.$ancienneImage);
            }
            
            // Génération d'un nouveau nom unique
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            
            // Déplacement du fichier
            $imageFile->move(
                $uploadDir,
                $newFilename
            );
            
            // Mise à jour de l'entité
            $produit->setImage($newFilename);
        } else {
            // Si aucun nouveau fichier, on garde l'ancien
            $produit->setImage($ancienneImage);
        }

        // Sauvegarde en base
        $entityManager->flush();

        // Message de confirmation
        $this->addFlash('success', 'Le produit a été mis à jour avec succès');

        // Redirection vers la fiche du produit
        return $this->redirectToRoute('app_produit_show', [
            'id_produit' => $produit->getIdProduit()
        ]);
    }

    return $this->render('produit/edit.html.twig', [
        'produit' => $produit,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id_produit}/delete', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, int $id_produit, ProduitRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $produit = $repository->find($id_produit);

        if (!$produit) {
            throw $this->createNotFoundException('Aucun produit trouvé pour l\'ID ' . $id_produit);
        }

        if ($this->isCsrfTokenValid('delete' . $produit->getId_produit(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
