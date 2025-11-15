<?php

namespace App\Features\Social\Services;

use App\Models\RecipeComment;
use App\Repositories\Contracts\CommentRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function __construct(RecipeComment $model)
    {
        parent::__construct($model);
    }
}
