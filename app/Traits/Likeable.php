<?php

namespace App\Traits;

use App\Models\RecipeLike;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait Likeable
{
    /**
     * Get all likes for the model.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(RecipeLike::class, 'recipe_id');
    }

    /**
     * Check if the model is liked by a user.
     */
    public function isLikedBy(int $userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Like the model.
     */
    public function like(int $userId): void
    {
        if (! $this->isLikedBy($userId)) {
            $this->likes()->create(['user_id' => $userId]);
            $this->increment('likes_count');
        }
    }

    /**
     * Unlike the model.
     */
    public function unlike(int $userId): void
    {
        if ($this->isLikedBy($userId)) {
            $this->likes()->where('user_id', $userId)->delete();
            $this->decrement('likes_count');
        }
    }

    /**
     * Toggle like status.
     */
    public function toggleLike(int $userId): bool
    {
        if ($this->isLikedBy($userId)) {
            $this->unlike($userId);
            return false;
        }

        $this->like($userId);
        return true;
    }
}
