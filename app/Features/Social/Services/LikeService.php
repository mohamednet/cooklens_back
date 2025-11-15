<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\RecipeLike;

class LikeService
{
    /**
     * Like a recipe.
     */
    public function likeRecipe(int $userId, int $recipeId): RecipeLike
    {
        $like = RecipeLike::create([
            'user_id' => $userId,
            'recipe_id' => $recipeId,
        ]);

        // Increment likes count
        Recipe::where('id', $recipeId)->increment('likes_count');

        return $like;
    }

    /**
     * Unlike a recipe.
     */
    public function unlikeRecipe(int $userId, int $recipeId): bool
    {
        $deleted = RecipeLike::where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->delete();

        if ($deleted) {
            Recipe::where('id', $recipeId)->decrement('likes_count');
        }

        return (bool) $deleted;
    }

    /**
     * Check if recipe is liked by user.
     */
    public function isLiked(int $userId, int $recipeId): bool
    {
        return RecipeLike::where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->exists();
    }

    /**
     * Get users who liked a recipe.
     */
    public function getRecipeLikes(int $recipeId, int $perPage = 15)
    {
        return RecipeLike::where('recipe_id', $recipeId)
            ->with('user')
            ->paginate($perPage);
    }
}
