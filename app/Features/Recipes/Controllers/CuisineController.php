<?php

namespace App\Features\Recipes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cuisine;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CuisineController extends Controller
{
    use ApiResponse;

    /**
     * List all cuisines.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Cuisine::withCount('recipes');

        // Filter by region if provided
        if ($request->has('region')) {
            $query->where('region', $request->input('region'));
        }

        $cuisines = $query->orderBy('name')->get();

        return $this->successResponse($cuisines);
    }

    /**
     * Get cuisine with recipes.
     */
    public function show(string $slug): JsonResponse
    {
        $cuisine = Cuisine::where('slug', $slug)
            ->with(['recipes' => function($query) {
                $query->where('status', 'published')
                    ->with(['creator', 'category'])
                    ->latest('published_at')
                    ->limit(20);
            }])
            ->withCount('recipes')
            ->firstOrFail();

        return $this->successResponse($cuisine);
    }

    /**
     * Create cuisine (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:cuisines,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'region' => ['nullable', 'string', 'max:100'],
            'image_url' => ['nullable', 'url'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $cuisine = Cuisine::create($validated);

        return $this->createdResponse($cuisine, 'Cuisine created successfully');
    }

    /**
     * Update cuisine (Admin only).
     */
    public function update(Request $request, Cuisine $cuisine): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:100', 'unique:cuisines,name,' . $cuisine->id],
            'description' => ['nullable', 'string', 'max:500'],
            'region' => ['nullable', 'string', 'max:100'],
            'image_url' => ['nullable', 'url'],
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $cuisine->update($validated);

        return $this->successResponse($cuisine, 'Cuisine updated successfully');
    }

    /**
     * Delete cuisine (Admin only).
     */
    public function destroy(Cuisine $cuisine): JsonResponse
    {
        // Check if cuisine has recipes
        if ($cuisine->recipes()->count() > 0) {
            return $this->errorResponse('Cannot delete cuisine with existing recipes', 400);
        }

        $cuisine->delete();

        return $this->successResponse(null, 'Cuisine deleted successfully');
    }
}
