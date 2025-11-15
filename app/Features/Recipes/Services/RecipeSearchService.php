<?php

namespace App\Features\Recipes\Services;

use App\Repositories\Contracts\RecipeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RecipeSearchService
{
    public function __construct(
        protected RecipeRepositoryInterface $recipeRepository
    ) {}

    /**
     * Search recipes by criteria.
     */
    public function search(array $criteria, int $perPage = 15): LengthAwarePaginator
    {
        return $this->recipeRepository->filter(function ($query) use ($criteria) {
            // Search by title or description
            if (! empty($criteria['search'])) {
                $query->where(function ($q) use ($criteria) {
                    $q->where('title', 'LIKE', "%{$criteria['search']}%")
                      ->orWhere('description', 'LIKE', "%{$criteria['search']}%");
                });
            }

            // Filter by category
            if (! empty($criteria['category_id'])) {
                $query->where('category_id', $criteria['category_id']);
            }

            // Filter by cuisine
            if (! empty($criteria['cuisine_id'])) {
                $query->where('cuisine_id', $criteria['cuisine_id']);
            }

            // Filter by difficulty
            if (! empty($criteria['difficulty'])) {
                $query->where('difficulty', $criteria['difficulty']);
            }

            // Filter by status
            if (! empty($criteria['status'])) {
                $query->where('status', $criteria['status']);
            } else {
                // Default: only published recipes
                $query->where('status', 'published');
            }

            // Filter by max prep time
            if (! empty($criteria['max_prep_time'])) {
                $query->where('prep_time', '<=', $criteria['max_prep_time']);
            }

            // Filter by max cook time
            if (! empty($criteria['max_cook_time'])) {
                $query->where('cook_time', '<=', $criteria['max_cook_time']);
            }

            // Sort by
            $sortBy = $criteria['sort_by'] ?? 'latest';
            match ($sortBy) {
                'popular' => $query->orderBy('views_count', 'desc'),
                'liked' => $query->orderBy('likes_count', 'desc'),
                'rated' => $query->orderBy('average_rating', 'desc'),
                default => $query->orderBy('created_at', 'desc'),
            };
        }, $perPage);
    }

    /**
     * Search recipes by ingredients.
     */
    public function searchByIngredients(array $ingredientIds, int $perPage = 15): LengthAwarePaginator
    {
        return $this->recipeRepository->filter(function ($query) use ($ingredientIds) {
            $query->whereHas('ingredients', function ($q) use ($ingredientIds) {
                $q->whereIn('ingredients.id', $ingredientIds);
            })
            ->where('status', 'published')
            ->orderBy('created_at', 'desc');
        }, $perPage);
    }
}
