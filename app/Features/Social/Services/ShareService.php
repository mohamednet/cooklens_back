<?php

namespace App\Features\Social\Services;

use App\Models\RecipeShare;

class ShareService
{
    /**
     * Track recipe share.
     */
    public function trackShare(int $userId, int $recipeId, string $platform): RecipeShare
    {
        return RecipeShare::create([
            'user_id' => $userId,
            'recipe_id' => $recipeId,
            'platform' => $platform,
        ]);
    }

    /**
     * Get share statistics for a recipe.
     */
    public function getRecipeShareStats(int $recipeId): array
    {
        $shares = RecipeShare::where('recipe_id', $recipeId)
            ->selectRaw('platform, COUNT(*) as count')
            ->groupBy('platform')
            ->get();

        return [
            'total' => $shares->sum('count'),
            'by_platform' => $shares->pluck('count', 'platform')->toArray(),
        ];
    }

    /**
     * Get user's share history.
     */
    public function getUserShares(int $userId, int $perPage = 15)
    {
        return RecipeShare::where('user_id', $userId)
            ->with('recipe')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
