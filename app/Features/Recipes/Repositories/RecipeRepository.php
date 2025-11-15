<?php

namespace App\Features\Recipes\Repositories;

use App\Models\Recipe;
use App\Repositories\Contracts\RecipeRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class RecipeRepository extends BaseRepository implements RecipeRepositoryInterface
{
    public function __construct(Recipe $model)
    {
        parent::__construct($model);
    }
}
