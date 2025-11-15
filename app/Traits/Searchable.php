<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Scope for searching records.
     */
    public function scopeSearch(Builder $query, ?string $term, array $columns = []): Builder
    {
        if (empty($term)) {
            return $query;
        }

        if (empty($columns)) {
            $columns = $this->searchable ?? ['name', 'title', 'description'];
        }

        return $query->where(function ($q) use ($term, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'LIKE', "%{$term}%");
            }
        });
    }

    /**
     * Scope for full-text search (if supported).
     */
    public function scopeFullTextSearch(Builder $query, ?string $term, array $columns = []): Builder
    {
        if (empty($term)) {
            return $query;
        }

        if (empty($columns)) {
            $columns = $this->searchable ?? ['title', 'description'];
        }

        $columnsString = implode(',', $columns);

        return $query->whereRaw(
            "MATCH({$columnsString}) AGAINST(? IN NATURAL LANGUAGE MODE)",
            [$term]
        );
    }
}
