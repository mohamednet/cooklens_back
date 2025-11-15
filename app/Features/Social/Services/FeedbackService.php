<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;

class FeedbackService
{
    /**
     * Submit feedback/review for a recipe.
     */
    public function submitFeedback(array $data): Feedback
    {
        return DB::transaction(function () use ($data) {
            $feedback = Feedback::create($data);

            // Update recipe average rating
            $this->updateRecipeRating($data['recipe_id']);

            return $feedback;
        });
    }

    /**
     * Update existing feedback.
     */
    public function updateFeedback(int $feedbackId, array $data): bool
    {
        $feedback = Feedback::find($feedbackId);

        if (! $feedback) {
            return false;
        }

        $updated = $feedback->update($data);

        if ($updated && isset($data['rating'])) {
            $this->updateRecipeRating($feedback->recipe_id);
        }

        return $updated;
    }

    /**
     * Delete feedback.
     */
    public function deleteFeedback(int $feedbackId): bool
    {
        $feedback = Feedback::find($feedbackId);

        if (! $feedback) {
            return false;
        }

        $recipeId = $feedback->recipe_id;
        $deleted = $feedback->delete();

        if ($deleted) {
            $this->updateRecipeRating($recipeId);
        }

        return $deleted;
    }

    /**
     * Get recipe feedback.
     */
    public function getRecipeFeedback(int $recipeId, int $perPage = 15)
    {
        return Feedback::where('recipe_id', $recipeId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Update recipe average rating.
     */
    protected function updateRecipeRating(int $recipeId): void
    {
        $averageRating = Feedback::where('recipe_id', $recipeId)
            ->avg('rating');

        Recipe::where('id', $recipeId)->update([
            'average_rating' => round($averageRating, 2),
        ]);
    }

    /**
     * Mark review as helpful.
     */
    public function markHelpful(int $feedbackId): bool
    {
        return (bool) Feedback::where('id', $feedbackId)->increment('helpful_count');
    }
}
