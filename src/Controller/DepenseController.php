<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Entity\Finance;
use App\Entity\RapportDepense;
use App\Entity\RapportRevenu;
use App\Form\DepenseType;
use App\Repository\DepenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/depense')]
final class DepenseController extends AbstractController
{
    #[Route(name: 'app_depense_index', methods: ['GET'])]
    public function index(DepenseRepository $depenseRepository): Response
    {
        return $this->render('depense/index.html.twig', [
            'depenses' => $depenseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_depense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,DepenseRepository $depenseRepository): Response
    {
        $depense = new Depense();
        $form = $this->createForm(DepenseType::class, $depense,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->beginTransaction();
        try {
            $existingDepense = $depenseRepository->findOneBy([
                'typeDep' => $depense->getTypeDep(),
                'montantDep' => $depense->getMontantDep(),
                'datereceptionDep' => $depense->getDatereceptionDep()
            ]);
    
            if ($existingDepense) {
                $this->addFlash('error', 'Cette dépense existe déjà !');
                return $this->render('depense/new.html.twig', [
                    'form' => $form->createView()
                ]);
            }
            $entityManager->persist($depense);
            $entityManager->flush();

            $date = $depense->getDatereceptionDep();
        $finance = $entityManager->getRepository(Finance::class)->findOneBy([
            'moisFin' => (int)$date->format('m'),
            'anneeFin' => (int)$date->format('Y')
        ]);
        if ($finance) {
            // Vérification si l'association existe déjà
            $existingRapport = $entityManager->getRepository(RapportDepense::class)->findOneBy([
                'depense' => $depense,
                'finance' => $finance
            ]);
            if (!$existingRapport) {
                $rapportDepense = new RapportDepense();
                $rapportDepense->setDepense($depense)
                              ->setFinance($finance);
                $entityManager->persist($rapportDepense);
                
                // Mise à jour des totaux
                $this->updateFinanceTotals($finance, $entityManager);
            }
        }
$entityManager->flush();
        // Met à jour les totaux

        $entityManager->commit();


            return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
        }catch (\Exception $e) {
            $entityManager->rollback();
            $this->addFlash('error', 'Une erreur est survenue: '.$e->getMessage());
        }
        
    }
        return $this->render('depense/new.html.twig', [
            'depense' => $depense,
            'form' => $form,
        ]);
    }

    /*#[Route('/{iddepense}', name: 'app_depense_show', methods: ['GET'])]
    public function show(Depense $depense): Response
    {
        return $this->render('depense/show.html.twig', [
            'depense' => $depense,
        ]);
    }*/

    #[Route('/{iddepense}', name: 'app_depense_show', methods: ['GET'])]
public function show(DepenseRepository $depenseRepository, int $iddepense): Response
{
    $depense = $depenseRepository->find($iddepense);

    if (!$depense) {
        throw $this->createNotFoundException('Dépense non trouvée.');
    }

    return $this->render('depense/show.html.twig', [
        'depense' => $depense,
    ]);
}

  /*  #[Route('/{iddepense}/edit', name: 'app_depense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depense $depense, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('depense/edit.html.twig', [
            'depense' => $depense,
            'form' => $form,
        ]);
    }*/
    #[Route('/{iddepense}/edit', name: 'app_depense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DepenseRepository $depenseRepository, EntityManagerInterface $entityManager, int $iddepense): Response
    {
        $depense = $entityManager->find(Depense::class, $iddepense);
        if (!$depense) {
            throw $this->createNotFoundException('Dépense non trouvée.');
        }
        $originalMontant = $depense->getMontantDep();
        $oldDate = clone $depense->getDatereceptionDep();
        $originalValues = [
            'type' => $depense->getTypeDep(),
            'montant' => $depense->getMontantDep(),
            'date' => $depense->getDatereceptionDep()
        ];
        $oldRapports = $entityManager->getRepository(RapportDepense::class)
        ->findBy(['depense' => $depense]);
        $oldFinances = array_map(fn($rapport) => $rapport->getFinance(), $oldRapports);
       

        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hasChanged = 
            $originalValues['type'] !== $depense->getTypeDep() ||
            $originalValues['montant'] !== $depense->getMontantDep() ||
            $originalValues['date'] != $depense->getDatereceptionDep();
            if ($hasChanged) {
                $entityManager->beginTransaction();
            try {
                // Vérifie l'existence d'une dépense identique (sauf la courante)
                $existingDepense = $depenseRepository->findOneBy([
                    'typeDep' => $depense->getTypeDep(),
                    'montantDep' => $depense->getMontantDep(),
                    'datereceptionDep' => $depense->getDatereceptionDep()
                ]);
    
                if ($existingDepense && $existingDepense->getIddepense() !== $depense->getIddepense()) {
                    $this->addFlash('error', 'Cette dépense existe déjà !');
                    
                    return $this->render('depense/edit.html.twig', [
                        'depense' => $depense,
                        'form' => $form->createView(),
                    ]);
                }$entityManager->persist($depense);
                $entityManager->flush();
                
                $dateChanged = $originalValues['date']->format('Y-m') !== $depense->getDatereceptionDep()->format('Y-m');
                $montantChanged = ($originalMontant !== $depense->getMontantDep());
                if ($dateChanged) {
                    // 1. Supprimer les anciennes associations
                    foreach ($oldRapports as $rapport) {
                        $entityManager->remove($rapport);
                    }
                    $entityManager->flush();
                    $newDate = $depense->getDatereceptionDep();
                    $newFinance = $entityManager->getRepository(Finance::class)->findOneBy([
                        'moisFin' => (int)$newDate->format('m'),
                        'anneeFin' => (int)$newDate->format('Y')
                    ]);
                    if ($newFinance) {
                        $newRapport = new RapportDepense();
                        $newRapport->setDepense($depense)
                                  ->setFinance($newFinance);
                        $entityManager->persist($newRapport);
                        
            
                    foreach ($oldFinances as $oldFinance) {
                        if ($oldFinance instanceof Finance) {
                            $this->updateFinanceTotals($oldFinance, $entityManager);
                        }
                    }
                // Mise à jour de la nouvelle finance
                $this->updateFinanceTotals($newFinance, $entityManager);
            }
            elseif ($montantChanged) {
                $rapports = $entityManager->getRepository(RapportDepense::class)
                    ->findBy(['depense' => $depense]);
                
                foreach ($rapports as $rapport) {
                    $finance = $rapport->getFinance();
                    $this->updateFinanceTotals($finance, $entityManager);
                }
            }
        }
            $entityManager->flush();
            $entityManager->commit();
            return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
        }
        catch (\Exception $e) {
            $entityManager->rollback();
            $this->addFlash('error', 'Une erreur est survenue lors de la modification: '.$e->getMessage());
        }
    } else {
        // Aucune modification détectée
        $this->addFlash('info', 'Aucune modification détectée.');
        return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
    }
}
    
        return $this->render('depense/edit.html.twig', [
            'depense' => $depense,
            'form' => $form->createView(),
        ]);
    }


   /* #[Route('/{iddepense}', name: 'app_depense_delete', methods: ['POST'])]
    public function delete(Request $request, Depense $depense, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$depense->getIddepense(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($depense);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
    }*/
    #[Route('/{iddepense}/delete', name: 'app_depense_delete', methods: ['POST'])]
    public function delete(Request $request, DepenseRepository $depenseRepository, EntityManagerInterface $entityManager, int $iddepense): Response
    {
       
        if (!$this->isCsrfTokenValid('delete'.$iddepense, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('app_depense_index');
        }
        $entityManager->beginTransaction();
    
    try {
        $depense = $entityManager->find(Depense::class, $iddepense);
        if (!$depense) {
        $this->addFlash('error', 'Dépense non trouvée');
        return $this->redirectToRoute('app_depense_index');
    }
        $rapports = $entityManager->getRepository(RapportDepense::class)
        ->findBy(['depense' => $depense]);

    foreach ($rapports as $rapport) {
        $finance = $rapport->getFinance();
        $entityManager->remove($rapport);
        $entityManager->flush();
        // Mise à jour des totaux après chaque suppression
        if ($finance) {
            $this->updateFinanceTotals($finance, $entityManager);
        }
        }
            $entityManager->remove($depense);
            $entityManager->flush();
            $entityManager->commit();
        
    } catch (\Exception $e) {
        $entityManager->rollback();
        $this->addFlash('error', 'Erreur lors de la suppression : '.$e->getMessage());
    }
        return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
    }



    private function updateFinanceTotals(Finance $finance, EntityManagerInterface $entityManager): void
{
    $freshFinance = $entityManager->find(Finance::class, $finance->getIdfinance());
    
    if (!$freshFinance) {
        return;
    }
    // Calcul des dépenses
    $totalDepenses = $entityManager->createQueryBuilder()
        ->select('COALESCE(SUM(d.montantDep), 0)')
        ->from(RapportDepense::class, 'rd')
        ->join('rd.depense', 'd')
        ->where('rd.finance = :finance')
        ->setParameter('finance', $freshFinance->getIdfinance())
        ->getQuery()
        ->getSingleScalarResult() ?? 0;

        $totalRevenus = $entityManager->createQueryBuilder()
        ->select('COALESCE(SUM(r.montantRevenu), 0)')
        ->from(RapportRevenu::class, 'rr')
        ->join('rr.revenu', 'r')
        ->where('rr.finance = :finance')
        ->setParameter('finance',$freshFinance->getIdfinance())
        ->getQuery()
        ->getSingleScalarResult() ?? 0;
    
        $freshFinance->setTotalDepenses((float)$totalDepenses)
        ->setTotalRevenus((float)$totalRevenus)
        ->setProfit((float)($totalRevenus - $totalDepenses));
        $entityManager->persist($freshFinance);
     $entityManager->flush();
}
}
