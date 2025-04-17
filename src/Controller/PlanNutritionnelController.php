<?php

namespace App\Controller;

use App\Entity\PlanNutritionnel;
use App\Form\PlanNutritionnelType;
use App\Repository\PlanNutritionnelRepository;
use App\Repository\ObjectifRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; 
use App\Repository\UserRepository;

#[Route('/plan/nutritionnel')]
final class PlanNutritionnelController extends AbstractController
{
    #[Route('/', name: 'app_plan_nutritionnel_index', methods: ['GET'])]
    public function index(ObjectifRepository $objectifRepository): Response
    {
        $objectifs = $objectifRepository->findAll();
        return $this->render('plan_nutritionnel/index.html.twig', [
            'objectifs' => $objectifs,
        ]);
    }

    #[Route('/new/{idObjectif}', name: 'app_plan_nutritionnel_new', methods: ['GET', 'POST'])]
    public function new(
        int $idObjectif,
        Request $request,
        EntityManagerInterface $entityManager,
        ObjectifRepository $objectifRepository,
        UserRepository $userRepository
    ): Response {
        $objectif = $objectifRepository->find($idObjectif);
        if (!$objectif) {
            throw $this->createNotFoundException('Objectif non trouvé.');
        }
    
      /*  $user = $userRepository->find(1);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur avec ID 1 non trouvé.');
        }*/
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Aucun utilisateur connecté.');
        }
        $planNutritionnel = new PlanNutritionnel();
        $planNutritionnel->setObjectif($objectif);
        $planNutritionnel->setUser($user);
    
        // AUTO-RÉGIME BASÉ SUR LA DESCRIPTION
        $description = strtolower($objectif->getDescription());
    
        $keywordsPerte = ['perte', 'perdre', 'maigrir', 'mincir', 'weight loss', 'lose weight', 'fat burn', 'slim','nedh3af','non9es','ntaya7','na9es'];
        $keywordsGain = ['prise', 'gagner', 'gain', 'muscler', 'weight gain', 'bulk', 'mass','nzid','nmascel','nesmen'];
    
        $texteRegimePerte = <<<EOD
    🥣 **Petit-déjeuner :**
    - 1 bol de flocons d’avoine avec du lait écrémé
    - 1 fruit (pomme ou banane)
    - 1 thé vert sans sucre
    
    🍽 **Déjeuner :**
    - 1 portion de blanc de poulet grillé
    - Légumes vapeur (brocoli, carottes, courgettes)
    - 1 petite portion de quinoa
    - Eau citronnée
    
    🥗 **Dîner :**
    - 1 soupe aux légumes
    - 1 œuf dur
    - Salade verte sans sauce industrielle
    
    🍌 **Collations :**
    - Une poignée d’amandes (non salées)
    - 1 yaourt nature 0%
    EOD;
    
        $texteRegimeGain = <<<EOD
    🥣 **Petit-déjeuner :**
    - 2 œufs entiers + pain complet
    - 1 bol de lait entier avec céréales complètes
    - 1 banane
    - 1 cuillère de beurre de cacahuète
    
    🍽 **Déjeuner :**
    - 1 steak haché 5% ou poisson gras (saumon)
    - Riz complet
    - Légumes sautés à l’huile d’olive
    - 1 fruit au choix
    
    🥗 **Dîner :**
    - Pâtes aux légumes et à la viande hachée
    - Fromage blanc
    - 1 tartine au miel
    
    🍌 **Collations :**
    - Shaker protéiné ou smoothie à base de lait, fruits, flocons d’avoine
    - Fruits secs (dattes, raisins secs)
    EOD;
    
        if (array_filter($keywordsPerte, fn($k) => str_contains($description, $k))) {
            $planNutritionnel->setRegime($texteRegimePerte);
        } elseif (array_filter($keywordsGain, fn($k) => str_contains($description, $k))) {
            $planNutritionnel->setRegime($texteRegimeGain);
        }
    
        // FORMULAIRE
        $form = $this->createForm(PlanNutritionnelType::class, $planNutritionnel);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($planNutritionnel);
            $entityManager->flush();
            $this->addFlash('success', 'Plan nutritionnel ajouté avec succès.');
            return $this->redirectToRoute('app_plan_nutritionnel_index');
        } elseif ($form->isSubmitted()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        }
    
        return $this->render('plan_nutritionnel/new.html.twig', [
            'form' => $form->createView(),
            'objectif' => $objectif,
        ]);
    }
    
    #[Route('/{idPlan}/edit', name: 'app_plan_nutritionnel_edit', methods: ['GET', 'POST'])]
    public function edit(
        int $idPlan,
        Request $request,
        PlanNutritionnelRepository $planNutritionnelRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $planNutritionnel = $planNutritionnelRepository->find($idPlan);
        if (!$planNutritionnel) {
            throw $this->createNotFoundException('Plan nutritionnel non trouvé.');
        }

        $form = $this->createForm(PlanNutritionnelType::class, $planNutritionnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Plan nutritionnel mis à jour avec succès.');
            return $this->redirectToRoute('app_plan_nutritionnel_index');
        } elseif ($form->isSubmitted()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        }

        return $this->render('plan_nutritionnel/edit.html.twig', [
            'form' => $form->createView(),
            'planNutritionnel' => $planNutritionnel,
        ]);
    }

    #[Route('/{idPlan}/delete', name: 'app_plan_nutritionnel_delete', methods: ['POST'])]
    public function delete(
        int $idPlan,
        Request $request,
        PlanNutritionnelRepository $planNutritionnelRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $planNutritionnel = $planNutritionnelRepository->find($idPlan);
        if (!$planNutritionnel) {
            throw $this->createNotFoundException('Plan nutritionnel non trouvé.');
        }

        if ($this->isCsrfTokenValid('delete' . $planNutritionnel->getIdPlan(), $request->request->get('_token'))) {
            $entityManager->remove($planNutritionnel);
            $entityManager->flush();
            $this->addFlash('success', 'Plan nutritionnel supprimé avec succès.');
        } else {
            $this->addFlash('danger', 'Token CSRF invalide. L\'opération a échoué.');
        }

        return $this->redirectToRoute('app_plan_nutritionnel_index');
    } 
    
    #[Route('/admin/plans-objectifs', name: 'app_plan_nutritionnel_admin_index', methods: ['GET'])]
public function adminIndex(PlanNutritionnelRepository $planRepo): Response
{
    // Récupérer tous les plans nutritionnels (sans méthode custom)
    $plans = $planRepo->findAll();
    
    return $this->render('plan_nutritionnel/admin_index.html.twig', [
        'plans' => $plans,
    ]);
}
}