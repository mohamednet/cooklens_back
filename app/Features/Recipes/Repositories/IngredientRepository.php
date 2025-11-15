<?php

namespace App\Features\Recipes\Repositories;

use App\Models\Ingredient;
use App\Repositories\Contracts\IngredientRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class IngredientRepository extends BaseRepository implements IngredientRepositoryInterface
{
    public function __construct(Ingredient $model)
    {
        parent::__construct($model);
    }
}
