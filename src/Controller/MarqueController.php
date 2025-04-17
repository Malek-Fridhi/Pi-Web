<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Form\MarqueType;
use App\Repository\MarqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/marque')]
final class MarqueController extends AbstractController
{
    #[Route(name: 'app_marque_index', methods: ['GET'])]
    public function index(MarqueRepository $marqueRepository): Response
    {
        return $this->render('marque/index.html.twig', [
            'marques' => $marqueRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_marque_new', methods: ['GET', 'POST'])]
   
    public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $marque = new Marque();
    $form = $this->createForm(MarqueType::class, $marque);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('image')->getData();
        
        if ($imageFile) {
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            
            // Déplacer le fichier
            $imageFile->move(
                $this->getParameter('marques_directory'),
                $newFilename
            );
            
            // Mettre à jour l'entité
            $marque->setImage($newFilename);
        }

        $entityManager->persist($marque);
        $entityManager->flush();

        return $this->redirectToRoute('app_marque_index');
    }

    return $this->render('marque/new.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route('/export-pdf', name: 'app_marque_export_pdf', methods: ['GET'])]
public function exportToPdf(MarqueRepository $marqueRepository): Response
{
    $marques = $marqueRepository->findAll();
    
    if (empty($marques)) {
        $this->addFlash('warning', 'Aucune marque à exporter');
        return $this->redirectToRoute('app_marque_index');
    }

    $html = $this->renderView('marque/export_pdf.html.twig', [
        'marques' => $marques,
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
            'Content-Disposition' => 'attachment; filename="liste_marques_'.date('Y-m-d_H-i').'.pdf"'
        ]
    );
}

 /*   #[Route('/{id_marque}', name: 'app_marque_show', methods: ['GET'])]
    public function show(Marque $marque): Response
    {
        return $this->render('marque/show.html.twig', [
            'marque' => $marque,
        ]);
    }

    #[Route('/{id_marque}/edit', name: 'app_marque_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Marque $marque, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_marque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('marque/edit.html.twig', [
            'marque' => $marque,
            'form' => $form,
        ]);
    }

    #[Route('/{id_marque}', name: 'app_marque_delete', methods: ['POST'])]
    public function delete(Request $request, Marque $marque, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marque->getId_marque(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($marque);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_marque_index', [], Response::HTTP_SEE_OTHER);
    }*/
    #[Route('/{id_marque}', name: 'app_marque_show', methods: ['GET'])]
    public function show(int $id_marque, MarqueRepository $repository): Response
    {
        $marque = $repository->find($id_marque);

        if (!$marque) {
            throw $this->createNotFoundException('Aucune marque trouvée pour l\'ID ' . $id_marque);
        }

        return $this->render('marque/show.html.twig', [
            'marque' => $marque,
        ]);
    }

    /*#[Route('/{id_marque}/edit', name: 'app_marque_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id_marque, MarqueRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $marque = $repository->find($id_marque);

        if (!$marque) {
            throw $this->createNotFoundException('Aucune marque trouvée pour l\'ID ' . $id_marque);
        }

        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_marque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('marque/edit.html.twig', [
            'marque' => $marque,
            'form' => $form,
        ]);
    }*/
    #[Route('/{id_marque}/edit', name: 'app_marque_edit', methods: ['GET', 'POST'])]
public function edit(
    Request $request,
    int $id_marque,
    MarqueRepository $repository,
    EntityManagerInterface $entityManager
): Response {
    $marque = $repository->find($id_marque);
    if (!$marque) {
        throw $this->createNotFoundException('Marque non trouvée');
    }

    // Sauvegarde de l'ancienne image
    $ancienneImage = $marque->getImage();
    $uploadDir = $this->getParameter('marques_directory');

    // Création du formulaire avec image non obligatoire en édition
    $form = $this->createForm(MarqueType::class, $marque, [
        'image_required' => false
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('image')->getData();
        
        if ($imageFile) {
            // Supprimer l'ancienne image si elle existe
            if ($ancienneImage && file_exists($uploadDir.'/'.$ancienneImage)) {
                unlink($uploadDir.'/'.$ancienneImage);
            }
            
            // Générer un nouveau nom de fichier
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            
            // Déplacer le fichier
            $imageFile->move(
                $uploadDir,
                $newFilename
            );
            
            $marque->setImage($newFilename);
        } else {
            // Conserver l'ancienne image si aucun nouveau fichier
            $marque->setImage($ancienneImage);
        }

        $entityManager->flush();

        $this->addFlash('success', 'La marque a été mise à jour avec succès');

        return $this->redirectToRoute('app_marque_show', [
            'id_marque' => $marque->getIdMarque()
        ], Response::HTTP_SEE_OTHER);
    }

    return $this->render('marque/edit.html.twig', [
        'marque' => $marque,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id_marque}/delete', name: 'app_marque_delete', methods: ['POST'])]
    public function delete(Request $request, int $id_marque, MarqueRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $marque = $repository->find($id_marque);

        if (!$marque) {
            throw $this->createNotFoundException('Aucune marque trouvée pour l\'ID ' . $id_marque);
        }

        if ($this->isCsrfTokenValid('delete' . $marque->getId_marque(), $request->request->get('_token'))) {
            $entityManager->remove($marque);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_marque_index', [], Response::HTTP_SEE_OTHER);
    }
}
