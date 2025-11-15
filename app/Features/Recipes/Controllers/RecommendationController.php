<?php

namespace App\Features\Recipes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    use ApiResponse;

    /**
     * Get personalized recommendations for user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->input('limit', 10);

        // Get user's favorite cuisines
        $favoriteCuisines = $user->favorites()
            ->with('recipe.cuisine')
            ->get()
            ->pluck('recipe.cuisine_id')
            ->filter()
            ->unique()
            ->take(3);

        // Get user's liked recipe categories
        $likedCategories = $user->recipeLikes()
            ->with('recipe.category')
            ->get()
            ->pluck('recipe.category_id')
            ->filter()
            ->unique()
            ->take(3);

        // Build recommendation query
        $query = Recipe::where('status', 'published')
            ->where('created_by', '!=', $user->id) // Exclude user's own recipes
            ->with(['creator', 'category', 'cuisine']);

        // Prioritize based on preferences
        if ($favoriteCuisines->isNotEmpty() || $likedCategories->isNotEmpty()) {
            $query->where(function($q) use ($favoriteCuisines, $likedCategories) {
                if ($favoriteCuisines->isNotEmpty()) {
                    $q->orWhereIn('cuisine_id', $favoriteCuisines);
                }
                if ($likedCategories->isNotEmpty()) {
                    $q->orWhereIn('category_id', $likedCategories);
                }
            });
        }

        // Order by popularity and rating
        $recommendations = $query->orderByDesc('likes_count')
            ->orderByDesc('average_rating')
            ->orderByDesc('views_count')
            ->limit($limit)
            ->get();

        // If not enough recommendations, add trending recipes
        if ($recommendations->count() < $limit) {
            $trending = Recipe::where('status', 'published')
                ->where('created_by', '!=', $user->id)
                ->whereNotIn('id', $recommendations->pluck('id'))
                ->with(['creator', 'category', 'cuisine'])
                ->orderByDesc('views_count')
                ->limit($limit - $recommendations->count())
                ->get();

            $recommendations = $recommendations->merge($trending);
        }

        return $this->successResponse($recommendations);
    }

    /**
     * Get trending recipes.
     */
    public function trending(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $days = $request->input('days', 7);

        $trending = Recipe::where('status', 'published')
            ->where('published_at', '>=', now()->subDays($days))
            ->with(['creator', 'category', 'cuisine'])
            ->orderByDesc('views_count')
            ->orderByDesc('likes_count')
            ->limit($limit)
            ->get();

        return $this->successResponse($trending);
    }

    /**
     * Get similar recipes.
     */
    public function similar(Recipe $recipe): JsonResponse
    {
        $similar = Recipe::where('status', 'published')
            ->where('id', '!=', $recipe->id)
            ->where(function($query) use ($recipe) {
                $query->where('category_id', $recipe->category_id)
                    ->orWhere('cuisine_id', $recipe->cuisine_id)
                    ->orWhere('difficulty', $recipe->difficulty);
            })
            ->with(['creator', 'category', 'cuisine'])
            ->orderByDesc('average_rating')
            ->orderByDesc('likes_count')
            ->limit(10)
            ->get();

        return $this->successResponse($similar);
    }

    /**
     * Get recipes by same creator.
     */
    public function byCreator(Recipe $recipe): JsonResponse
    {
        $byCreator = Recipe::where('status', 'published')
            ->where('created_by', $recipe->created_by)
            ->where('id', '!=', $recipe->id)
            ->with(['creator', 'category', 'cuisine'])
            ->orderByDesc('likes_count')
            ->orderByDesc('views_count')
            ->limit(10)
            ->get();

        return $this->successResponse($byCreator);
    }
}
