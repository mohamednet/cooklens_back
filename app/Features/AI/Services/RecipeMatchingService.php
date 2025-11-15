<?php

namespace App\Features\AI\Services;

use App\Models\Recipe;

class RecipeMatchingService
{
    /**
     * Calculate match percentage between user ingredients and recipe.
     */
    public function calculateMatch(array $userIngredientIds, Recipe $recipe): float
    {
        $recipeIngredientIds = $recipe->ingredients->pluck('id')->toArray();

        if (empty($recipeIngredientIds)) {
            return 0;
        }

        $matchCount = count(array_intersect($userIngredientIds, $recipeIngredientIds));
        $totalIngredients = count($recipeIngredientIds);

        return round(($matchCount / $totalIngredients) * 100, 2);
    }

    /**
     * Get missing ingredients for a recipe.
     */
    public function getMissingIngredients(array $userIngredientIds, Recipe $recipe): array
    {
        $recipeIngredientIds = $recipe->ingredients->pluck('id')->toArray();

        return array_diff($recipeIngredientIds, $userIngredientIds);
    }

    /**
     * Find recipes user can make with available ingredients.
     */
    public function findMakeableRecipes(array $ingredientIds, float $minMatchPercentage = 80): array
    {
        $recipes = Recipe::where('status', 'published')
            ->with('ingredients')
            ->get();

        $makeable = [];

        foreach ($recipes as $recipe) {
            $matchPercentage = $this->calculateMatch($ingredientIds, $recipe);

            if ($matchPercentage >= $minMatchPercentage) {
                $makeable[] = [
                    'recipe' => $recipe,
                    'match_percentage' => $matchPercentage,
                    'missing_ingredients' => $this->getMissingIngredients($ingredientIds, $recipe),
                ];
            }
        }

        // Sort by match percentage
        usort($makeable, fn($a, $b) => $b['match_percentage'] <=> $a['match_percentage']);

        return $makeable;
    }
}
