<?php

namespace App\Features\Recipes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    use ApiResponse;

    /**
     * List all ingredients.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 50);
        
        $query = Ingredient::query();

        // Search by name
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $ingredients = $query->orderBy('name')->paginate($perPage);

        return $this->successResponse($ingredients);
    }

    /**
     * Get ingredient with recipes.
     */
    public function show(Ingredient $ingredient): JsonResponse
    {
        $ingredient->load(['recipes' => function($query) {
            $query->where('status', 'published')
                ->with(['creator', 'category', 'cuisine'])
                ->latest('published_at')
                ->limit(20);
        }]);

        return $this->successResponse($ingredient);
    }

    /**
     * Create ingredient (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:ingredients,name'],
            'category' => ['nullable', 'string', 'max:50'],
            'calories_per_100g' => ['nullable', 'integer', 'min:0'],
            'protein_per_100g' => ['nullable', 'numeric', 'min:0'],
            'carbs_per_100g' => ['nullable', 'numeric', 'min:0'],
            'fat_per_100g' => ['nullable', 'numeric', 'min:0'],
        ]);

        $ingredient = Ingredient::create($validated);

        return $this->createdResponse($ingredient, 'Ingredient created successfully');
    }

    /**
     * Update ingredient (Admin only).
     */
    public function update(Request $request, Ingredient $ingredient): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:100', 'unique:ingredients,name,' . $ingredient->id],
            'category' => ['nullable', 'string', 'max:50'],
            'calories_per_100g' => ['nullable', 'integer', 'min:0'],
            'protein_per_100g' => ['nullable', 'numeric', 'min:0'],
            'carbs_per_100g' => ['nullable', 'numeric', 'min:0'],
            'fat_per_100g' => ['nullable', 'numeric', 'min:0'],
        ]);

        $ingredient->update($validated);

        return $this->successResponse($ingredient, 'Ingredient updated successfully');
    }

    /**
     * Delete ingredient (Admin only).
     */
    public function destroy(Ingredient $ingredient): JsonResponse
    {
        // Check if ingredient is used in recipes
        if ($ingredient->recipes()->count() > 0) {
            return $this->errorResponse('Cannot delete ingredient used in recipes', 400);
        }

        $ingredient->delete();

        return $this->successResponse(null, 'Ingredient deleted successfully');
    }

    /**
     * Bulk import ingredients (Admin only).
     */
    public function bulkImport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ingredients' => ['required', 'array'],
            'ingredients.*.name' => ['required', 'string', 'max:100'],
            'ingredients.*.category' => ['nullable', 'string', 'max:50'],
        ]);

        $imported = 0;
        $skipped = 0;

        foreach ($validated['ingredients'] as $ingredientData) {
            $exists = Ingredient::where('name', $ingredientData['name'])->exists();
            
            if (!$exists) {
                Ingredient::create($ingredientData);
                $imported++;
            } else {
                $skipped++;
            }
        }

        return $this->successResponse([
            'imported' => $imported,
            'skipped' => $skipped,
            'total' => count($validated['ingredients'])
        ], 'Bulk import completed');
    }
}
