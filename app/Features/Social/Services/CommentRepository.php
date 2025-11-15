<?php

namespace App\Repositories\Eloquent;

use App\Models\RecipeComment;
use App\Repositories\Contracts\CommentRepositoryInterface;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function __construct(RecipeComment $model)
    {
        parent::__construct($model);
    }
}
