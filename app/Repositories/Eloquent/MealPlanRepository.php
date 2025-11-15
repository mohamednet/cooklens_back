<?php

namespace App\Repositories\Eloquent;

use App\Models\MealPlan;
use App\Repositories\Contracts\MealPlanRepositoryInterface;

class MealPlanRepository extends BaseRepository implements MealPlanRepositoryInterface
{
    public function __construct(MealPlan $model)
    {
        parent::__construct($model);
    }
}
