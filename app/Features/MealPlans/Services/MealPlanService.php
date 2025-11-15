<?php

namespace App\Features\MealPlans\Services;

use App\Models\MealPlan;
use App\Models\MealPlanRecipe;
use App\Repositories\Contracts\MealPlanRepositoryInterface;

class MealPlanService
{
    public function __construct(
        protected MealPlanRepositoryInterface $mealPlanRepository
    ) {}

    /**
     * Create a new meal plan.
     */
    public function create(array $data): MealPlan
    {
        return $this->mealPlanRepository->create($data);
    }

    /**
     * Update meal plan.
     */
    public function update(int $mealPlanId, array $data): bool
    {
        return $this->mealPlanRepository->update($mealPlanId, $data);
    }

    /**
     * Delete meal plan (soft delete).
     */
    public function delete(int $mealPlanId): bool
    {
        return $this->mealPlanRepository->delete($mealPlanId);
    }

    /**
     * Add recipe to meal plan.
     */
    public function addRecipe(int $mealPlanId, int $recipeId, string $plannedDate, string $mealType): MealPlanRecipe
    {
        return MealPlanRecipe::create([
            'meal_plan_id' => $mealPlanId,
            'recipe_id' => $recipeId,
            'planned_date' => $plannedDate,
            'meal_type' => $mealType,
        ]);
    }

    /**
     * Remove recipe from meal plan.
     */
    public function removeRecipe(int $mealPlanId, int $recipeId): bool
    {
        return (bool) MealPlanRecipe::where('meal_plan_id', $mealPlanId)
            ->where('recipe_id', $recipeId)
            ->delete();
    }

    /**
     * Get user's meal plans.
     */
    public function getUserMealPlans(int $userId, int $perPage = 15)
    {
        return $this->mealPlanRepository->filter(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->with('recipes')
                ->orderBy('start_date', 'desc');
        }, $perPage);
    }

    /**
     * Get meal plan details.
     */
    public function getMealPlanDetails(int $mealPlanId)
    {
        return MealPlan::with(['recipes', 'mealPlanRecipes.recipe'])
            ->find($mealPlanId);
    }

    /**
     * Calculate total calories for meal plan.
     */
    public function calculateTotalCalories(int $mealPlanId): int
    {
        $mealPlan = MealPlan::with('recipes')->find($mealPlanId);

        if (! $mealPlan) {
            return 0;
        }

        return $mealPlan->recipes->sum('calories');
    }
}
