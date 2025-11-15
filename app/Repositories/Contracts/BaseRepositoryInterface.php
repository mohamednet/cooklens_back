<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    /**
     * Get all records.
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Paginate records.
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Find a record by its primary key.
     */
    public function find(int|string $id, array $columns = ['*']): ?Model;

    /**
     * Create a new record.
     */
    public function create(array $data): Model;

    /**
     * Update an existing record by its primary key.
     */
    public function update(int|string $id, array $data): bool;

    /**
     * Delete a record by its primary key.
     */
    public function delete(int|string $id): bool;

    /**
     * Basic search helper.
     *
     * @param array $criteria key => value pairs (e.g. ['title' => 'pasta'])
     */
    public function search(array $criteria = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Filter using a callback on the underlying query.
     */
    public function filter(callable $callback, int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;
}
