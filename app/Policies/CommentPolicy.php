<?php

namespace App\Policies;

use App\Models\RecipeComment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine if the user can view any comments.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view approved comments
    }

    /**
     * Determine if the user can create comments.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can comment
    }

    /**
     * Determine if the user can update the comment.
     */
    public function update(User $user, RecipeComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine if the user can delete the comment.
     */
    public function delete(User $user, RecipeComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine if the user can reply to the comment.
     */
    public function reply(User $user, RecipeComment $comment): bool
    {
        return true; // All authenticated users can reply
    }
}
