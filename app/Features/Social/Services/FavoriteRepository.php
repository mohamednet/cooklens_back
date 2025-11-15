<?php

namespace App\Features\Social\Services;

use App\Models\Favorite;
use App\Repositories\Contracts\FavoriteRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class FavoriteRepository extends BaseRepository implements FavoriteRepositoryInterface
{
    public function __construct(Favorite $model)
    {
        parent::__construct($model);
    }
}
