<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/equipement')]
final class EquipementController extends AbstractController
{
    #[Route('/stats', name: 'app_equipement_stats', methods: ['GET'])]
    public function stats(EquipementRepository $equipementRepository): Response
    {
        $equipements = $equipementRepository->findAll();
        
        $labels = [];
        $quantities = [];
        
        foreach ($equipements as $equipement) {
            $labels[] = $equipement->getNom();
            $quantities[] = $equipement->getQuantite();
        }
        
        return $this->render('equipement/index.html.twig', [
            'labels' => $labels,
            'quantities' => $quantities,
        ]);
    }

    #[Route('/new', name: 'app_equipement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('equipements_directory'),
                    $newFilename
                );
                $equipement->setImage($newFilename);
            }

            $entityManager->persist($equipement);
            $entityManager->flush();

            return $this->redirectToRoute('app_equipement_index');
        }

        return $this->render('equipement/new.html.twig', [
            'equipement' => $equipement,
            'form' => $form,
        ]);
    }
    #[Route('/export-pdf', name: 'app_equipement_export_pdf', methods: ['GET'])]
public function exportToPdf(EquipementRepository $equipementRepository): Response
{
    $equipements = $equipementRepository->findAll();
    
    if (empty($equipements)) {
        $this->addFlash('warning', 'Aucun équipement à exporter');
        return $this->redirectToRoute('app_equipement_index');
    }

    $html = $this->renderView('equipement/export_pdf.html.twig', [
        'equipements' => $equipements,
        'date' => new \DateTime()
    ]);
    
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    return new Response(
        $dompdf->output(),
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="liste_equipements.pdf"'
        ]
    );
}

    #[Route('/{id_equipement}', name: 'app_equipement_show', methods: ['GET'])]
    public function show(EquipementRepository $equipementRepository, $id_equipement): Response
    {
        // Conversion sécurisée en integer
        $id = is_numeric($id_equipement) ? (int)$id_equipement : 0;
        $equipement = $equipementRepository->find($id);

        if (!$equipement) {
            throw $this->createNotFoundException('Équipement non trouvé.');
        }

        return $this->render('equipement/show.html.twig', [
            'equipement' => $equipement,
        ]);
    }
    #[Route(name: 'app_equipement_index', methods: ['GET'])]
    public function index(EquipementRepository $equipementRepository): Response
    {
        $equipements = $equipementRepository->findAll();
        
        $labels = [];
        $quantities = [];
        
        foreach ($equipements as $equipement) {
            $labels[] = $equipement->getNom();
            $quantities[] = $equipement->getQuantite();
        }

        return $this->render('equipement/index.html.twig', [
            'equipements' => $equipements,
            'chartData' => [
                'labels' => $labels,
                'quantities' => $quantities,
            ],
        ]);
    }

    #[Route('/{id_equipement}/edit', name: 'app_equipement_edit', methods: ['GET', 'POST'])]
public function edit(
    Request $request,
    int $id_equipement,
    EquipementRepository $repository,
    EntityManagerInterface $entityManager
): Response {
    // Récupération de l'équipement
    $equipement = $repository->find($id_equipement);
    if (!$equipement) {
        throw $this->createNotFoundException('Équipement non trouvé');
    }

    // Sauvegarde de l'ancien nom d'image
    $ancienneImage = $equipement->getImage();
    $uploadDir = $this->getParameter('equipements_directory');

    // Création du formulaire avec l'option image_required
    $form = $this->createForm(EquipementType::class, $equipement, [
        'image_required' => false // Maintenant cette option est reconnue
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
            $equipement->setImage($newFilename);
        } else {
            // Si aucun nouveau fichier, on garde l'ancien
            $equipement->setImage($ancienneImage);
        }

        // Sauvegarde en base
        $entityManager->flush();

        // Message de confirmation
        $this->addFlash('success', 'L\'équipement a été mis à jour avec succès');

        // Redirection vers la fiche de l'équipement
        return $this->redirectToRoute('app_equipement_show', [
            'id_equipement' => $equipement->getIdEquipement()
        ]);
    }

    return $this->render('equipement/edit.html.twig', [
        'equipement' => $equipement,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id_equipement}/delete', name: 'app_equipement_delete', methods: ['POST'])]
    public function delete(Request $request, EquipementRepository $equipementRepository, $id_equipement, EntityManagerInterface $entityManager): Response
    {
        $id = is_numeric($id_equipement) ? (int)$id_equipement : 0;
        $equipement = $equipementRepository->find($id);

        if (!$equipement) {
            $this->addFlash('error', 'Équipement non trouvé');
            return $this->redirectToRoute('app_equipement_index');
        }

        if ($this->isCsrfTokenValid('delete'.$equipement->getIdEquipement(), $request->request->get('_token'))) {
            try {
                if ($equipement->getImage()) {
                    $imagePath = $this->getParameter('equipements_directory').'/'.$equipement->getImage();
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                $entityManager->remove($equipement);
                $entityManager->flush();
                $this->addFlash('success', 'Équipement supprimé avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression : '.$e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide');
        }

        return $this->redirectToRoute('app_equipement_index');
    }


    

}