<?php

namespace App\Features\Recipes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RecipeCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * List all categories.
     */
    public function index(): JsonResponse
    {
        $categories = RecipeCategory::withCount('recipes')
            ->orderBy('name')
            ->get();

        return $this->successResponse($categories);
    }

    /**
     * Get category with recipes.
     */
    public function show(string $slug): JsonResponse
    {
        $category = RecipeCategory::where('slug', $slug)
            ->with(['recipes' => function($query) {
                $query->where('status', 'published')
                    ->with(['creator', 'cuisine'])
                    ->latest('published_at')
                    ->limit(20);
            }])
            ->withCount('recipes')
            ->firstOrFail();

        return $this->successResponse($category);
    }

    /**
     * Create category (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:recipe_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = RecipeCategory::create($validated);

        return $this->createdResponse($category, 'Category created successfully');
    }

    /**
     * Update category (Admin only).
     */
    public function update(Request $request, RecipeCategory $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:100', 'unique:recipe_categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return $this->successResponse($category, 'Category updated successfully');
    }

    /**
     * Delete category (Admin only).
     */
    public function destroy(RecipeCategory $category): JsonResponse
    {
        // Check if category has recipes
        if ($category->recipes()->count() > 0) {
            return $this->errorResponse('Cannot delete category with existing recipes', 400);
        }

        $category->delete();

        return $this->successResponse(null, 'Category deleted successfully');
    }
}
