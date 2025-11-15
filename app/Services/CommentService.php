<?php

namespace App\Services;

use App\Models\RecipeComment;
use App\Repositories\Contracts\CommentRepositoryInterface;

class CommentService
{
    public function __construct(
        protected CommentRepositoryInterface $commentRepository
    ) {}

    /**
     * Create a comment on a recipe.
     */
    public function createComment(array $data): RecipeComment
    {
        return $this->commentRepository->create($data);
    }

    /**
     * Update a comment.
     */
    public function updateComment(int $commentId, array $data): bool
    {
        return $this->commentRepository->update($commentId, $data);
    }

    /**
     * Delete a comment (soft delete).
     */
    public function deleteComment(int $commentId): bool
    {
        return $this->commentRepository->delete($commentId);
    }

    /**
     * Get comments for a recipe.
     */
    public function getRecipeComments(int $recipeId, int $perPage = 15)
    {
        return $this->commentRepository->filter(function ($query) use ($recipeId) {
            $query->where('recipe_id', $recipeId)
                ->whereNull('parent_id')
                ->where('is_approved', true)
                ->with(['user', 'replies.user'])
                ->orderBy('created_at', 'desc');
        }, $perPage);
    }

    /**
     * Reply to a comment.
     */
    public function replyToComment(int $parentId, array $data): RecipeComment
    {
        $data['parent_id'] = $parentId;

        return $this->commentRepository->create($data);
    }

    /**
     * Approve a comment (admin).
     */
    public function approveComment(int $commentId): bool
    {
        return $this->commentRepository->update($commentId, ['is_approved' => true]);
    }

    /**
     * Reject a comment (admin).
     */
    public function rejectComment(int $commentId): bool
    {
        return $this->commentRepository->update($commentId, ['is_approved' => false]);
    }
}
