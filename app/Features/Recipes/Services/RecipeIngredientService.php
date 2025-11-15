<?php

namespace App\Features\Recipes\Services;

use App\Models\Recipe;
use App\Models\RecipeIngredient;

class RecipeIngredientService
{
    /**
     * Add ingredient to recipe.
     */
    public function addIngredient(int $recipeId, int $ingredientId, float $quantity, string $unit, ?string $notes = null): RecipeIngredient
    {
        return RecipeIngredient::create([
            'recipe_id' => $recipeId,
            'ingredient_id' => $ingredientId,
            'quantity' => $quantity,
            'unit' => $unit,
            'notes' => $notes,
        ]);
    }

    /**
     * Update recipe ingredient.
     */
    public function updateIngredient(int $ingredientId, array $data): bool
    {
        $ingredient = RecipeIngredient::find($ingredientId);

        if (! $ingredient) {
            return false;
        }

        return $ingredient->update($data);
    }

    /**
     * Remove ingredient from recipe.
     */
    public function removeIngredient(int $recipeId, int $ingredientId): bool
    {
        return (bool) RecipeIngredient::where('recipe_id', $recipeId)
            ->where('ingredient_id', $ingredientId)
            ->delete();
    }

    /**
     * Sync recipe ingredients (replace all).
     */
    public function syncIngredients(Recipe $recipe, array $ingredients): void
    {
        // Remove existing ingredients
        RecipeIngredient::where('recipe_id', $recipe->id)->delete();

        // Add new ingredients
        foreach ($ingredients as $ingredient) {
            $this->addIngredient(
                $recipe->id,
                $ingredient['ingredient_id'],
                $ingredient['quantity'],
                $ingredient['unit']
            );
        }
    }

    /**
     * Get all ingredients for a recipe.
     */
    public function getRecipeIngredients(int $recipeId)
    {
        return RecipeIngredient::where('recipe_id', $recipeId)
            ->with('ingredient')
            ->get();
    }
}
