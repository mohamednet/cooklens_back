<?php

namespace App\Features\Recipes\Controllers;

use App\Features\Recipes\Resources\RecipeListResource;
use App\Features\Recipes\Services\RecipeSearchService;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeSearchController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected RecipeSearchService $searchService
    ) {}

    /**
     * Search recipes with filters.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:recipe_categories,id'],
            'cuisine_id' => ['nullable', 'exists:cuisines,id'],
            'difficulty' => ['nullable', 'in:easy,medium,hard'],
            'prep_time_max' => ['nullable', 'integer', 'min:0'],
            'cook_time_max' => ['nullable', 'integer', 'min:0'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'sort' => ['nullable', 'in:latest,popular,liked,rated,favorited'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = $validated['per_page'] ?? 15;
        $recipes = $this->searchService->search($validated, $perPage);

        return $this->successResponse(
            RecipeListResource::collection($recipes)->response()->getData(true)
        );
    }

    /**
     * Search recipes by ingredients.
     */
    public function searchByIngredients(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ingredient_ids' => ['required', 'array'],
            'ingredient_ids.*' => ['exists:ingredients,id'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = $validated['per_page'] ?? 15;
        $recipes = $this->searchService->searchByIngredients(
            $validated['ingredient_ids'],
            $perPage
        );

        return $this->successResponse(
            RecipeListResource::collection($recipes)->response()->getData(true)
        );
    }
}
