<?php

namespace App\Controller;

use App\Entity\Finance;
use App\Entity\RapportRevenu;
use App\Entity\RapportDepense;
use App\Entity\Revenu;
use App\Entity\Depense;

use App\Form\FinanceType;
use App\Repository\FinanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/finance')]
final class FinanceController extends AbstractController
{
    #[Route(name: 'app_finance_index', methods: ['GET'])]
    public function index(FinanceRepository $financeRepository): Response
    {
        return $this->render('finance/index.html.twig', [
            'finances' => $financeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_finance_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $finance = new Finance();
    $form = $this->createForm(FinanceType::class, $finance);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérifier si un rapport existe déjà
        $existingReport = $entityManager->getRepository(Finance::class)->findOneBy([
            'moisFin' => $finance->getMoisFin(),
            'anneeFin' => $finance->getAnneeFin()
        ]);

        if ($existingReport) {
            $this->addFlash('error', 'Un rapport existe déjà pour ce mois et cette année.');
        } else {
            // Créer les dates de début et fin du mois
            $startDate = new \DateTime(sprintf(
                '%d-%d-01 00:00:00',
                $finance->getAnneeFin(),
                $finance->getMoisFin()
            ));
            
            $endDate = clone $startDate;
            $endDate->modify('last day of this month')->setTime(23, 59, 59);

            $revenus = $entityManager->getRepository(Revenu::class)
                ->createQueryBuilder('r')
                ->where('r.datereceptionRevenu BETWEEN :start AND :end')
                ->andWhere('NOT EXISTS (
                    SELECT rr FROM App\Entity\RapportRevenu rr 
                    WHERE rr.revenu = r
                )')
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getResult();

                $depenses = $entityManager->getRepository(Depense::class)
                ->createQueryBuilder('d')
                ->where('d.datereceptionDep BETWEEN :start AND :end')
                ->andWhere('NOT EXISTS (
                    SELECT rd FROM App\Entity\RapportDepense rd 
                    WHERE rd.depense = d
                )')
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getResult();

                foreach ($revenus as $revenu) {
                    $rapportRevenu = new RapportRevenu();
                    $rapportRevenu->setRevenu($revenu);
                    $rapportRevenu->setFinance($finance);
                    $entityManager->persist($rapportRevenu);
                    $finance->addRevenu($rapportRevenu);
                }

                foreach ($depenses as $depense) {
                    $rapportDepense = new RapportDepense();
                    $rapportDepense->setDepense($depense);
                    $rapportDepense->setFinance($finance);
                    $entityManager->persist($rapportDepense);
                    $finance->addDepense($rapportDepense);
                }

            // Calculer les totaux
            $totalRevenus = array_reduce($revenus, fn($c, $r) => $c + $r->getMontantRevenu(), 0);
            $totalDepenses = array_reduce($depenses, fn($c, $d) => $c + $d->getMontantDep(), 0);
            $profit = $totalRevenus - $totalDepenses;

            $finance->setTotalRevenus($totalRevenus)
                   ->setTotalDepenses($totalDepenses)
                   ->setProfit($profit);

            try {
                $entityManager->persist($finance);
                $entityManager->flush();
                $this->addFlash('success', 'Rapport créé avec succès avec ' . count($revenus) . ' revenus et ' . count($depenses) . ' dépenses associés.');
                return $this->redirectToRoute('app_finance_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: ' . $e->getMessage());
            }
        }
    }

    return $this->render('finance/new.html.twig', [
        'finance' => $finance,
        'form' => $form,
    ]);
}

   /* #[Route('/{idfinance}', name: 'app_finance_show', methods: ['GET'])]
    public function show(Finance $finance): Response
    {
        return $this->render('finance/show.html.twig', [
            'finance' => $finance,
        ]);
    }

    #[Route('/{idfinance}/edit', name: 'app_finance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Finance $finance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FinanceType::class, $finance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_finance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('finance/edit.html.twig', [
            'finance' => $finance,
            'form' => $form,
        ]);
    }

    #[Route('/{idfinance}', name: 'app_finance_delete', methods: ['POST'])]
    public function delete(Request $request, Finance $finance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$finance->getIdfinance(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($finance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_finance_index', [], Response::HTTP_SEE_OTHER);
    }*/
    #[Route('/{idfinance}', name: 'app_finance_show', methods: ['GET'])]
public function show($idfinance, EntityManagerInterface $entityManager): Response
{
    $finance = $entityManager->getRepository(Finance::class)->find($idfinance);

    if (!$finance) {
        throw $this->createNotFoundException('No finance found for id ' . $idfinance);
    }

    // Récupérer les revenus associés via les rapports
    $revenus = $finance->getRevenus()->map(function(RapportRevenu $rapport) {
        return [
            'id' => $rapport->getRevenu()->getIdrevenu(),
            'source' => $rapport->getRevenu()->getSourceRevenu(),
            'montant' => $rapport->getRevenu()->getMontantRevenu(),
            'date' => $rapport->getRevenu()->getDatereceptionRevenu()
        ];
    })->toArray();

    // Récupérer les dépenses associées via les rapports
    $depenses = $finance->getDepenses()->map(function(RapportDepense $rapport) {
        return [
            'id' => $rapport->getDepense()->getIddepense(),
            'type' => $rapport->getDepense()->getTypeDep(),
            'montant' => $rapport->getDepense()->getMontantDep(),
            'date' => $rapport->getDepense()->getDatereceptionDep()
        ];
    })->toArray();

    return $this->render('finance/show.html.twig', [
        'finance' => $finance,
        'revenus' => $revenus,
        'depenses' => $depenses
    ]);
}

#[Route('/{idfinance}/edit', name: 'app_finance_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, int $idfinance, EntityManagerInterface $entityManager): Response
{
    $finance = $entityManager->getRepository(Finance::class)->find($idfinance);
    if (!$finance) {
        throw $this->createNotFoundException('Aucun rapport trouvé pour l\'id ' . $idfinance);
    }

    $originalMois = $finance->getMoisFin();
    $originalAnnee = $finance->getAnneeFin();

    $form = $this->createForm(FinanceType::class, $finance);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
       
        // Vérifier si un rapport existe déjà pour ce mois et cette année
        $hasChanged = ($originalMois != $finance->getMoisFin()) || 
                     ($originalAnnee != $finance->getAnneeFin());

        if ($hasChanged) {
            // Vérifier si un rapport existe déjà pour le nouveau mois/année
            $existingReport = $entityManager->getRepository(Finance::class)->findOneBy([
                'moisFin' => $finance->getMoisFin(),
                'anneeFin' => $finance->getAnneeFin()
            ]);

            if ($existingReport && $existingReport->getIdfinance() !== $finance->getIdfinance()) {
                $this->addFlash('error', 'Un rapport existe déjà pour ce mois et cette année.');
                return $this->redirectToRoute('app_finance_edit', ['idfinance' => $idfinance]);
            }

            // Recréer les associations seulement si la date a changé
            $this->recreateAssociations($finance, $entityManager);
        }

        try {
            $entityManager->flush();
            $message = $hasChanged 
                ? 'Rapport mis à jour avec succès.' 
                : 'Aucune modification détectée.';
            $this->addFlash('success', $message);
            return $this->redirectToRoute('app_finance_index');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    return $this->render('finance/edit.html.twig', [
        'finance' => $finance,
        'form' => $form,
    ]);
}

    private function updateAssociations(
        Finance $finance,
        array $currentIds,
        array $selectedIds,
        string $entityClass,
        string $rapportClass,
        string $removeMethod,
        string $addMethod,
        EntityManagerInterface $entityManager
    ): void {
        // Supprimer les associations qui ne sont plus sélectionnées
        foreach (array_diff($currentIds, $selectedIds) as $idToRemove) {
            $rapport = $entityManager->getRepository($rapportClass)
                ->findOneBy([
                    'finance' => $finance,
                    strtolower(substr($entityClass, strrpos($entityClass, '\\') + 1)) => $idToRemove
                ]);
            
            if ($rapport) {
                $finance->$removeMethod($rapport);
                $entityManager->remove($rapport);
            }
        }

        // Ajouter les nouvelles associations
        foreach (array_diff($selectedIds, $currentIds) as $idToAdd) {
            $entity = $entityManager->getRepository($entityClass)->find($idToAdd);
            if ($entity) {
                $rapport = new $rapportClass();
                $setter = 'set' . substr($entityClass, strrpos($entityClass, '\\') + 1);
                $rapport->$setter($entity);
                $rapport->setFinance($finance);
                $finance->$addMethod($rapport);
                $entityManager->persist($rapport);
            }
        }
    }

    private function recreateAssociations(Finance $finance, EntityManagerInterface $entityManager): void
    {
        // Supprimer les anciennes associations
        foreach ($finance->getRevenus() as $rapportRevenu) {
            $entityManager->remove($rapportRevenu);
        }
        $finance->getRevenus()->clear();
    
        foreach ($finance->getDepenses() as $rapportDepense) {
            $entityManager->remove($rapportDepense);
        }
        $finance->getDepenses()->clear();
    
        // Créer les dates pour le nouveau mois/année
        $startDate = new \DateTime(sprintf(
            '%d-%d-01 00:00:00',
            $finance->getAnneeFin(),
            $finance->getMoisFin()
        ));
        
        $endDate = clone $startDate;
        $endDate->modify('last day of this month')->setTime(23, 59, 59);
    
        // Recréer les associations
        $revenus = $entityManager->getRepository(Revenu::class)
            ->createQueryBuilder('r')
            ->where('r.datereceptionRevenu BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getResult();
    
        foreach ($revenus as $revenu) {
            $rapportRevenu = new RapportRevenu();
            $rapportRevenu->setRevenu($revenu);
            $rapportRevenu->setFinance($finance);
            $entityManager->persist($rapportRevenu);
            $finance->addRevenu($rapportRevenu);
        }
        $depenses = $entityManager->getRepository(Depense::class)
        ->createQueryBuilder('r')
        ->where('r.datereceptionDep BETWEEN :start AND :end')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate)
        ->getQuery()
        ->getResult();

    foreach ($depenses as $depense) {
        $rapportDepense = new RapportDepense();
        $rapportDepense->setDepense($depense);
        $rapportDepense->setFinance($finance);
        $entityManager->persist($rapportDepense);
        $finance->addDepense($rapportDepense);
    }
       
    
        // Recalculer les totaux
        $this->calculateAndSetFinanceTotals($finance, $entityManager);
    }

    private function calculateAndSetFinanceTotals(Finance $finance, EntityManagerInterface $entityManager): void
    {
        $totalRevenus = array_reduce($finance->getRevenus()->toArray(), 
            fn($carry, $rapport) => $carry + $rapport->getRevenu()->getMontantRevenu(), 0);
        
        $totalDepenses = array_reduce($finance->getDepenses()->toArray(), 
            fn($carry, $rapport) => $carry + $rapport->getDepense()->getMontantDep(), 0);
        
        $finance
            ->setTotalRevenus($totalRevenus)
            ->setTotalDepenses($totalDepenses)
            ->setProfit($totalRevenus - $totalDepenses);
            
        $entityManager->persist($finance);
    }


    #[Route('/{idfinance}/delete', name: 'app_finance_delete', methods: ['POST'])]
    public function delete(Request $request, $idfinance, FinanceRepository $financeRepository, EntityManagerInterface $entityManager): Response
    {
        // Fetch the finance entity using the provided idfinance
        $finance = $financeRepository->find($idfinance);

        if (!$finance) {
            throw $this->createNotFoundException('No finance found for id ' . $idfinance);
        }

        if ($this->isCsrfTokenValid('delete' . $finance->getIdfinance(), $request->request->get('_token'))) {
            $entityManager->remove($finance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_finance_index', [], Response::HTTP_SEE_OTHER);
    }


}
