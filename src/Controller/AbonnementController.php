<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Revenu;
use App\Entity\RapportRevenu;
use App\Entity\Finance;
use App\Entity\RapportDepense;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/abonnement')]
final class AbonnementController extends AbstractController
{
    #[Route(name: 'app_abonnement_index', methods: ['GET'])]
    public function index(AbonnementRepository $abonnementRepository): Response
    {
        return $this->render('abonnement/index.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_abonnement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $abonnement = new Abonnement();
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->beginTransaction();
        
        try {
            $dateDebut = $abonnement->getDateDebut();
            $dateFin = (clone $dateDebut)->modify('+1 month');
    
            $prixMap = [
                'Silver' => 50,
                'Gold' => 65,
                'Diamond' => 80,
                'Premium' => 95
            ];
    
            $abonnement->setDateFin($dateFin);
            $abonnement->setPrix($prixMap[$abonnement->getType()] ?? 0);
            $abonnement->setEtat($dateFin > new \DateTime() ? 'Actif' : 'Expiré');
    
            $entityManager->persist($abonnement);

            // Création et enregistrement du revenu associé
            $revenu = new Revenu();
            $revenu->setSourceRevenu('Abonnement ' . $abonnement->getType());
            $revenu->setMontantRevenu($prixMap[$abonnement->getType()] ?? 0);
            $revenu->setDatereceptionRevenu(new \DateTime()); // Date système actuelle

            $entityManager->persist($revenu);

            $entityManager->flush();
            $finance = $entityManager->getRepository(Finance::class)->findOneBy([
                'moisFin' => (int)$dateDebut->format('m'),
                'anneeFin' => (int)$dateDebut->format('Y')
            ]);
            
            
            $rapportRevenu = new RapportRevenu();
            $rapportRevenu->setRevenu($revenu)
                         ->setFinance($finance);
            
            $entityManager->persist($rapportRevenu);
            $entityManager->flush();
            // Mise à jour des totaux financiers
            $this->updateFinanceTotals($finance, $entityManager);
            
            $entityManager->commit();

            return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }
    catch (\Exception $e) {
            $entityManager->rollback();
            $this->addFlash('error', 'Erreur lors de la création: '.$e->getMessage());
        }
    }
        return $this->render('abonnement/new.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/newf', name: 'app_abonnement_newf', methods: ['GET', 'POST'])]
    public function newf(Request $request, EntityManagerInterface $entityManager): Response
    {
        $abonnement = new Abonnement();
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $dateDebut = $abonnement->getDateDebut();
            $dateFin = (clone $dateDebut)->modify('+1 month');
    
            $prixMap = [
                'SLiver' => 50,
                'Gold' => 65,
                'Diamond' => 80,
                'Premium' => 95
            ];
    
            $abonnement->setDateFin($dateFin);
            $abonnement->setPrix($prixMap[$abonnement->getType()] ?? 0);
            $abonnement->setEtat($dateFin > new \DateTime() ? 'Actif' : 'Expiré');
    
            $entityManager->persist($abonnement);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('abonnement/newf.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_abonnement_show', methods: ['GET'])]
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('abonnement/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_abonnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_abonnement_delete', methods: ['POST'])]
    public function delete(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($abonnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
    }

    private function updateFinanceTotals(Finance $finance, EntityManagerInterface $entityManager): void
{
    // Recharger l'entité fraîche
    $freshFinance = $entityManager->find(Finance::class, $finance->getIdfinance());
    
    // Calcul des totaux
    $totalRevenus = $entityManager->createQueryBuilder()
        ->select('COALESCE(SUM(r.montantRevenu), 0)')
        ->from(RapportRevenu::class, 'rr')
        ->join('rr.revenu', 'r')
        ->where('rr.finance = :finance')
        ->setParameter('finance', $freshFinance)
        ->getQuery()
        ->getSingleScalarResult();

    $totalDepenses = $entityManager->createQueryBuilder()
        ->select('COALESCE(SUM(d.montantDep), 0)')
        ->from(RapportDepense::class, 'rd')
        ->join('rd.depense', 'd')
        ->where('rd.finance = :finance')
        ->setParameter('finance', $freshFinance)
        ->getQuery()
        ->getSingleScalarResult();

    $freshFinance->setTotalRevenus((float)$totalRevenus)
                ->setTotalDepenses((float)$totalDepenses)
                ->setProfit((float)($totalRevenus - $totalDepenses));

    $entityManager->persist($freshFinance);
    $entityManager->flush();
}
}
