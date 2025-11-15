<?php

namespace App\Services;

use App\Models\AiSuggestion;
use App\Models\AiSuggestionRecipe;
use App\Models\Recipe;

class AiSuggestionService
{
    /**
     * Generate recipe suggestions based on ingredients.
     */
    public function generateSuggestions(int $userId, array $ingredientIds): AiSuggestion
    {
        // Create AI suggestion record
        $suggestion = AiSuggestion::create([
            'user_id' => $userId,
            'ingredients_list' => $ingredientIds,
        ]);

        // Find matching recipes
        $recipes = $this->findMatchingRecipes($ingredientIds);

        // Store suggestion results
        foreach ($recipes as $recipe) {
            AiSuggestionRecipe::create([
                'ai_suggestion_id' => $suggestion->id,
                'recipe_id' => $recipe['id'],
                'match_percentage' => $recipe['match_percentage'],
            ]);
        }

        return $suggestion->load('recipes');
    }

    /**
     * Find recipes that match the given ingredients.
     */
    protected function findMatchingRecipes(array $ingredientIds): array
    {
        $recipes = Recipe::where('status', 'published')
            ->with('ingredients')
            ->get();

        $matches = [];

        foreach ($recipes as $recipe) {
            $recipeIngredientIds = $recipe->ingredients->pluck('id')->toArray();
            $matchCount = count(array_intersect($ingredientIds, $recipeIngredientIds));
            $totalIngredients = count($recipeIngredientIds);

            if ($matchCount > 0) {
                $matchPercentage = ($matchCount / $totalIngredients) * 100;

                $matches[] = [
                    'id' => $recipe->id,
                    'match_percentage' => round($matchPercentage, 2),
                ];
            }
        }

        // Sort by match percentage
        usort($matches, fn($a, $b) => $b['match_percentage'] <=> $a['match_percentage']);

        return array_slice($matches, 0, 10); // Top 10 matches
    }

    /**
     * Get suggestion results.
     */
    public function getSuggestion(int $suggestionId)
    {
        return AiSuggestion::with(['recipes', 'aiSuggestionRecipes.recipe'])
            ->find($suggestionId);
    }

    /**
     * Get user's suggestion history.
     */
    public function getUserHistory(int $userId, int $perPage = 15)
    {
        return AiSuggestion::where('user_id', $userId)
            ->with('recipes')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
