<?php

namespace App\Features\Recipes\Controllers;

use App\Features\Recipes\Services\RecipeIngredientService;
use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeIngredientController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected RecipeIngredientService $ingredientService
    ) {}

    /**
     * Add ingredient to recipe.
     */
    public function store(Request $request, Recipe $recipe): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('update', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to modify this recipe');
        }

        $validated = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $this->ingredientService->addIngredient(
            $recipe->id,
            $validated['ingredient_id'],
            $validated['quantity'],
            $validated['unit'],
            $validated['notes'] ?? null
        );

        return $this->successResponse(
            $recipe->fresh('ingredients')->ingredients,
            'Ingredient added successfully'
        );
    }

    /**
     * Update ingredient in recipe.
     */
    public function update(Request $request, Recipe $recipe, int $ingredientId): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('update', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to modify this recipe');
        }

        $validated = $request->validate([
            'quantity' => ['sometimes', 'numeric', 'min:0'],
            'unit' => ['sometimes', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $this->ingredientService->updateIngredient($recipe->id, $ingredientId, $validated);

        return $this->successResponse(
            $recipe->fresh('ingredients')->ingredients,
            'Ingredient updated successfully'
        );
    }

    /**
     * Remove ingredient from recipe.
     */
    public function destroy(Request $request, Recipe $recipe, int $ingredientId): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('update', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to modify this recipe');
        }

        $this->ingredientService->removeIngredient($recipe->id, $ingredientId);

        return $this->successResponse(null, 'Ingredient removed successfully');
    }

    /**
     * Sync all ingredients for recipe.
     */
    public function sync(Request $request, Recipe $recipe): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('update', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to modify this recipe');
        }

        $validated = $request->validate([
            'ingredients' => ['required', 'array'],
            'ingredients.*.ingredient_id' => ['required', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['required', 'numeric', 'min:0'],
            'ingredients.*.unit' => ['required', 'string', 'max:50'],
            'ingredients.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $this->ingredientService->syncIngredients($recipe->id, $validated['ingredients']);

        return $this->successResponse(
            $recipe->fresh('ingredients')->ingredients,
            'Ingredients synced successfully'
        );
    }
}
