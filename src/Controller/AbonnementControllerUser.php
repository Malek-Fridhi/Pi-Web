<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\RapportDepense;
use App\Entity\RapportRevenu;
use App\Entity\Finance;
use App\Entity\Revenu;
use App\Entity\Depense;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/newAbonnement')]
final class AbonnementControllerUser extends AbstractController
{
    #[Route(name: 'app_abonnement_index_User', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $abonnement = new Abonnement();
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour réserver un service.');
            return $this->redirectToRoute('app_login');
        }
        $abonnement->setUser($user);
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

            return $this->redirectToRoute('app_cour_indexx', [], Response::HTTP_SEE_OTHER);
        }
    catch (\Exception $e) {
            $entityManager->rollback();
            $this->addFlash('error', 'Erreur lors de la création: '.$e->getMessage());
        }
    }
        return $this->render('abonnement/newUser.html.twig', [
            'form' => $form,
        ]);
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