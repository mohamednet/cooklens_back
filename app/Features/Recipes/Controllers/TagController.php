<?php

namespace App\Features\Recipes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    use ApiResponse;

    /**
     * List all tags.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tag::withCount('recipes');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        $tags = $query->orderBy('name')->get();

        return $this->successResponse($tags);
    }

    /**
     * Get tag with recipes.
     */
    public function show(string $slug): JsonResponse
    {
        $tag = Tag::where('slug', $slug)
            ->with(['recipes' => function($query) {
                $query->where('status', 'published')
                    ->with(['creator', 'category', 'cuisine'])
                    ->latest('published_at')
                    ->limit(20);
            }])
            ->withCount('recipes')
            ->firstOrFail();

        return $this->successResponse($tag);
    }

    /**
     * Create tag (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:tags,name'],
            'type' => ['required', 'in:dietary,cuisine,meal_type,cooking_method,occasion,difficulty,other'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $tag = Tag::create($validated);

        return $this->createdResponse($tag, 'Tag created successfully');
    }

    /**
     * Update tag (Admin only).
     */
    public function update(Request $request, Tag $tag): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:50', 'unique:tags,name,' . $tag->id],
            'type' => ['sometimes', 'in:dietary,cuisine,meal_type,cooking_method,occasion,difficulty,other'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $tag->update($validated);

        return $this->successResponse($tag, 'Tag updated successfully');
    }

    /**
     * Delete tag (Admin only).
     */
    public function destroy(Tag $tag): JsonResponse
    {
        // Detach from all recipes first
        $tag->recipes()->detach();
        
        $tag->delete();

        return $this->successResponse(null, 'Tag deleted successfully');
    }
}
