<?php

namespace App\Services;

use App\Models\Recipe;
use App\Repositories\Contracts\RecipeRepositoryInterface;
use Illuminate\Support\Str;

class RecipeService
{
    public function __construct(
        protected RecipeRepositoryInterface $recipeRepository
    ) {}

    /**
     * Create a new recipe.
     */
    public function create(array $data): Recipe
    {
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['status'] = $data['status'] ?? 'draft';

        return $this->recipeRepository->create($data);
    }

    /**
     * Update an existing recipe.
     */
    public function update(int $recipeId, array $data): bool
    {
        if (isset($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $recipeId);
        }

        return $this->recipeRepository->update($recipeId, $data);
    }

    /**
     * Delete a recipe (soft delete).
     */
    public function delete(int $recipeId): bool
    {
        return $this->recipeRepository->delete($recipeId);
    }

    /**
     * Get recipe by ID.
     */
    public function find(int $recipeId): ?Recipe
    {
        return $this->recipeRepository->find($recipeId);
    }

    /**
     * Get all recipes with pagination.
     */
    public function paginate(int $perPage = 15)
    {
        return $this->recipeRepository->paginate($perPage);
    }

    /**
     * Publish a recipe.
     */
    public function publish(int $recipeId): bool
    {
        return $this->recipeRepository->update($recipeId, [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Increment views count.
     */
    public function incrementViews(Recipe $recipe): void
    {
        $recipe->increment('views_count');
    }

    /**
     * Generate unique slug for recipe.
     */
    protected function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists.
     */
    protected function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Recipe::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
