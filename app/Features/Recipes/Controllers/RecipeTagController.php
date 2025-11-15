<?php

namespace App\Features\Recipes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeTagController extends Controller
{
    use ApiResponse;

    /**
     * Attach tags to recipe.
     */
    public function attach(Request $request, Recipe $recipe): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('update', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to modify this recipe');
        }

        $validated = $request->validate([
            'tag_ids' => ['required', 'array'],
            'tag_ids.*' => ['required', 'exists:tags,id'],
        ]);

        // Attach tags (won't duplicate)
        $recipe->tags()->syncWithoutDetaching($validated['tag_ids']);

        return $this->successResponse(
            $recipe->fresh('tags')->tags,
            'Tags attached successfully'
        );
    }

    /**
     * Detach tags from recipe.
     */
    public function detach(Request $request, Recipe $recipe): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('update', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to modify this recipe');
        }

        $validated = $request->validate([
            'tag_ids' => ['required', 'array'],
            'tag_ids.*' => ['required', 'exists:tags,id'],
        ]);

        $recipe->tags()->detach($validated['tag_ids']);

        return $this->successResponse(
            $recipe->fresh('tags')->tags,
            'Tags detached successfully'
        );
    }

    /**
     * Sync tags (replace all).
     */
    public function sync(Request $request, Recipe $recipe): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('update', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to modify this recipe');
        }

        $validated = $request->validate([
            'tag_ids' => ['required', 'array'],
            'tag_ids.*' => ['required', 'exists:tags,id'],
        ]);

        $recipe->tags()->sync($validated['tag_ids']);

        return $this->successResponse(
            $recipe->fresh('tags')->tags,
            'Tags synced successfully'
        );
    }
}
