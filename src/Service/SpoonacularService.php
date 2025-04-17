<?php
// src/Service/SpoonacularService.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpoonacularService
{
    private $client;
    private $apiKey;
    private $baseUrl = 'https://api.spoonacular.com';

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function searchRecipes(array $criteria = []): array
    {
        $defaultParams = [
            'apiKey' => $this->apiKey,
            'addRecipeInformation' => true,
            'addRecipeNutrition' => true,
            'number' => 12,
            'fillIngredients' => true,
            'instructionsRequired' => true
        ];

        $response = $this->client->request(
            'GET', 
            $this->baseUrl.'/recipes/complexSearch', 
            [
                'query' => array_merge($defaultParams, $criteria),
                'timeout' => 30
            ]
        );

        $data = $response->toArray();
        
        foreach ($data['results'] as &$recipe) {
            if (!isset($recipe['nutrition'])) {
                $recipe['nutrition'] = ['nutrients' => []];
            }
        }
        
        return $data;
    }

    public function getRecipeDetails(int $id): array
    {
        try {
            // Debug: Affiche l'URL qui sera appelée
            $url = $this->baseUrl.'/recipes/'.$id.'/information?apiKey='.$this->apiKey.'&includeNutrition=true';
            dump("URL API: ".$url);
    
            $response = $this->client->request(
                'GET', 
                $url,
                [
                    'timeout' => 30
                ]
            );
    
            // Vérifie le code de statut HTTP
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw new \Exception("L'API a retourné le code $statusCode");
            }
    
            $data = $response->toArray();
            
            // Debug: Affiche la réponse complète de l'API
            dump("Réponse API:", $data);
    
            // Vérification de la structure des données
            if (!isset($data['nutrition']['nutrients'])) {
                throw new \Exception("Les données nutritionnelles sont manquantes dans la réponse");
            }
    
            // Transformation des nutriments en format plus accessible
            $nutrients = [];
            foreach ($data['nutrition']['nutrients'] as $nutrient) {
                if (!isset($nutrient['name'], $nutrient['amount'], $nutrient['unit'])) {
                    continue; // Saute les entrées incomplètes
                }
                $nutrients[$nutrient['name']] = [
                    'amount' => $nutrient['amount'],
                    'unit' => $nutrient['unit']
                ];
            }
    
            // Ajoute les nutriments transformés à la réponse
            $data['nutrition']['nutrients'] = $nutrients;
    
            return $data;
    
        } catch (\Exception $e) {
            // En mode dev, affiche l'erreur complète
            dump("ERREUR: ".$e->getMessage());
            
            // Retourne une structure vide mais valide pour le template
            return [
                'title' => 'Recette non disponible',
                'image' => null,
                'instructions' => '',
                'extendedIngredients' => [],
                'nutrition' => [
                    'nutrients' => []
                ]
            ];
        }
    }
}