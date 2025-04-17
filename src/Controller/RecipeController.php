<?php
// src/Controller/RecipeController.php

namespace App\Controller;

use App\Service\SpoonacularService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'app_recipes')]
    public function index(SpoonacularService $spoonacular, Request $request, SessionInterface $session): Response
    {
        $searchQuery = $request->query->get('search', '');
        $ingredients = $request->query->get('ingredients', '');

        $criteria = [];
        
        if (!empty($searchQuery)) {
            $criteria['query'] = $searchQuery;
        }
        
        if (!empty($ingredients)) {
            $criteria['includeIngredients'] = $ingredients;
        }

        try {
            $recipes = $spoonacular->searchRecipes($criteria);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la recherche : '.$e->getMessage());
            $recipes = ['results' => []];
        }

        // Récupérer les repas consommés depuis la session
        $consumedMeals = $session->get('consumed_meals', []);
        $totalCalories = array_sum(array_column($consumedMeals, 'calories'));

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes['results'] ?? [],
            'search_query' => $searchQuery,
            'ingredients_query' => $ingredients,
            'consumed_meals' => $consumedMeals,
            'total_calories' => $totalCalories
        ]);
    }

    #[Route('/recette/{id}', name: 'app_recipe_show')]
    public function show(int $id, SpoonacularService $spoonacular, SessionInterface $session): Response
    {
        try {
            $recipe = $spoonacular->getRecipeDetails($id);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Recette non trouvée');
            return $this->redirectToRoute('app_recipes');
        }

        // Récupérer les repas consommés depuis la session
        $consumedMeals = $session->get('consumed_meals', []);
        $totalCalories = array_sum(array_column($consumedMeals, 'calories'));

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'consumed_meals' => $consumedMeals,
            'total_calories' => $totalCalories
        ]);
    }

    #[Route('/ajouter-repas/{id}', name: 'app_add_meal')]
    public function addMeal(int $id, SpoonacularService $spoonacular, Request $request): Response
    {
        try {
            $recipe = $spoonacular->getRecipeDetails($id);
            
            $session = $request->getSession();
            $consumedMeals = $session->get('consumed_meals', []);
            
            $consumedMeals[] = [
                'id' => $recipe['id'],
                'title' => $recipe['title'],
                'calories' => $recipe['nutrition']['nutrients']['Calories']['amount'] ?? 0
            ];
            
            $session->set('consumed_meals', $consumedMeals);
            
            if ($request->isXmlHttpRequest()) {
                $totalCalories = array_sum(array_column($consumedMeals, 'calories'));
                return new JsonResponse([
                    'success' => true,
                    'meals' => $consumedMeals,
                    'totalCalories' => $totalCalories
                ]);
            }
            
            $this->addFlash('success', 'Repas ajouté avec succès!');
        } catch (\Exception $e) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'error' => 'Erreur lors de l\'ajout du repas']);
            }
            $this->addFlash('error', 'Erreur lors de l\'ajout du repas');
        }
        
        return $this->redirectToRoute('app_recipes');
    }

    #[Route('/supprimer-repas/{index}', name: 'app_remove_meal')]
    public function removeMeal(int $index, Request $request): Response
    {
        $session = $request->getSession();
        $consumedMeals = $session->get('consumed_meals', []);
        
        if (isset($consumedMeals[$index])) {
            unset($consumedMeals[$index]);
            $consumedMeals = array_values($consumedMeals);
            $session->set('consumed_meals', $consumedMeals);
            
            if ($request->isXmlHttpRequest()) {
                $totalCalories = array_sum(array_column($consumedMeals, 'calories'));
                return new JsonResponse([
                    'success' => true,
                    'meals' => $consumedMeals,
                    'totalCalories' => $totalCalories
                ]);
            }
            
            $this->addFlash('success', 'Repas supprimé avec succès!');
        }
        
        return $this->redirectToRoute('app_recipes');
    }

    #[Route('/vider-repas', name: 'app_clear_meals')]
    public function clearMeals(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('consumed_meals');
        
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'meals' => [],
                'totalCalories' => 0
            ]);
        }
        
        $this->addFlash('success', 'Tous les repas ont été supprimés!');
        return $this->redirectToRoute('app_recipes');
    }
}