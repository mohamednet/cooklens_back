<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Recipe;
use App\Repositories\Contracts\FavoriteRepositoryInterface;

class FavoriteService
{
    public function __construct(
        protected FavoriteRepositoryInterface $favoriteRepository
    ) {}

    /**
     * Add recipe to favorites.
     */
    public function addFavorite(int $userId, int $recipeId): Favorite
    {
        $favorite = $this->favoriteRepository->create([
            'user_id' => $userId,
            'recipe_id' => $recipeId,
        ]);

        // Increment favorites count
        Recipe::where('id', $recipeId)->increment('favorites_count');

        return $favorite;
    }

    /**
     * Remove recipe from favorites.
     */
    public function removeFavorite(int $userId, int $recipeId): bool
    {
        $deleted = Favorite::where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->delete();

        if ($deleted) {
            Recipe::where('id', $recipeId)->decrement('favorites_count');
        }

        return (bool) $deleted;
    }

    /**
     * Check if recipe is favorited by user.
     */
    public function isFavorited(int $userId, int $recipeId): bool
    {
        return Favorite::where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->exists();
    }

    /**
     * Get user's favorites.
     */
    public function getUserFavorites(int $userId, int $perPage = 15)
    {
        return $this->favoriteRepository->filter(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->with('recipe')
                ->orderBy('created_at', 'desc');
        }, $perPage);
    }
}
