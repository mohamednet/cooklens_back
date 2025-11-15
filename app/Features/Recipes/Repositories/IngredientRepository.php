<?php

namespace App\Repositories\Eloquent;

use App\Models\Ingredient;
use App\Repositories\Contracts\IngredientRepositoryInterface;

class IngredientRepository extends BaseRepository implements IngredientRepositoryInterface
{
    public function __construct(Ingredient $model)
    {
        parent::__construct($model);
    }
}
