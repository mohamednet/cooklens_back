<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->query()->get($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->query()->select($columns)->paginate($perPage);
    }

    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->query()->select($columns)->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int|string $id, array $data): bool
    {
        $model = $this->find($id);

        if (! $model) {
            return false;
        }

        return $model->update($data);
    }

    public function delete(int|string $id): bool
    {
        $model = $this->find($id);

        if (! $model) {
            return false;
        }

        return (bool) $model->delete();
    }

    public function search(array $criteria = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->query();

        foreach ($criteria as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            // Simple LIKE search by default
            $query->where($field, 'LIKE', "%{$value}%");
        }

        return $query->select($columns)->paginate($perPage);
    }

    public function filter(callable $callback, int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->query();

        $callback($query);

        return $query->select($columns)->paginate($perPage);
    }
}
