<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;

class RecipePolicy
{
    /**
     * Determine if the user can view any recipes.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view published recipes
    }

    /**
     * Determine if the user can view the recipe.
     */
    public function view(?User $user, Recipe $recipe): bool
    {
        // Published recipes are public
        if ($recipe->status === 'published') {
            return true;
        }

        // Draft recipes can only be viewed by the owner
        return $user && $user->id === $recipe->user_id;
    }

    /**
     * Determine if the user can create recipes.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create recipes
    }

    /**
     * Determine if the user can update the recipe.
     */
    public function update(User $user, Recipe $recipe): bool
    {
        return $user->id === $recipe->user_id;
    }

    /**
     * Determine if the user can delete the recipe.
     */
    public function delete(User $user, Recipe $recipe): bool
    {
        return $user->id === $recipe->user_id;
    }

    /**
     * Determine if the user can publish the recipe.
     */
    public function publish(User $user, Recipe $recipe): bool
    {
        return $user->id === $recipe->user_id && $recipe->status === 'draft';
    }
}
