<?php

namespace App\Traits;

trait HasCounters
{
    /**
     * Increment a counter field.
     */
    public function incrementCounter(string $field, int $amount = 1): bool
    {
        return $this->increment($field, $amount);
    }

    /**
     * Decrement a counter field.
     */
    public function decrementCounter(string $field, int $amount = 1): bool
    {
        return $this->decrement($field, $amount);
    }

    /**
     * Reset a counter field.
     */
    public function resetCounter(string $field): bool
    {
        return $this->update([$field => 0]);
    }

    /**
     * Increment views count.
     */
    public function incrementViews(): bool
    {
        return $this->incrementCounter('views_count');
    }

    /**
     * Increment likes count.
     */
    public function incrementLikes(): bool
    {
        return $this->incrementCounter('likes_count');
    }

    /**
     * Decrement likes count.
     */
    public function decrementLikes(): bool
    {
        return $this->decrementCounter('likes_count');
    }

    /**
     * Increment favorites count.
     */
    public function incrementFavorites(): bool
    {
        return $this->incrementCounter('favorites_count');
    }

    /**
     * Decrement favorites count.
     */
    public function decrementFavorites(): bool
    {
        return $this->decrementCounter('favorites_count');
    }
}
