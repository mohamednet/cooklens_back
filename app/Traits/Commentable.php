<?php

namespace App\Traits;

use App\Models\RecipeComment;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait Commentable
{
    /**
     * Get all comments for the model.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(RecipeComment::class, 'recipe_id');
    }

    /**
     * Get approved comments only.
     */
    public function approvedComments(): HasMany
    {
        return $this->comments()->where('is_approved', true);
    }

    /**
     * Get top-level comments (no parent).
     */
    public function topLevelComments(): HasMany
    {
        return $this->comments()
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->with('replies');
    }

    /**
     * Add a comment.
     */
    public function addComment(int $userId, string $comment, ?int $parentId = null): RecipeComment
    {
        return $this->comments()->create([
            'user_id' => $userId,
            'comment' => $comment,
            'parent_id' => $parentId,
            'is_approved' => false, // Requires moderation
        ]);
    }
}
