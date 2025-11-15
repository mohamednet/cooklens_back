<?php

namespace App\Features\MealPlans\Repositories;

use App\Models\MealPlan;
use App\Repositories\Contracts\MealPlanRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class MealPlanRepository extends BaseRepository implements MealPlanRepositoryInterface
{
    public function __construct(MealPlan $model)
    {
        parent::__construct($model);
    }
}
