<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SearcchTrait
{
    /**
     * Scope search
     */
    public function scopeSearch(Builder $query, ?string $keyword, ?array $columns = null): Builder
    {
        if (! $keyword) {
            return $query;
        }

        $searchColumns = $columns ?? $this->getSearchableColumns();
        $keyword       = trim($keyword);

        return $query->where(function (Builder $q) use ($searchColumns, $keyword) {
            foreach ($searchColumns as $column) {
                if (str_contains($column, '.')) {
                    $this->addRelationSearch($q, $column, $keyword);
                } else {
                    $q->orWhere($column, 'like', "%{$keyword}%");
                }
            }
        });
    }

    /**
     * Scope filter
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status)
        );

        $query->when($filters['category_id'] ?? null, fn ($q, $category) => $q->where('category_id', $category)
        );

        $query->when($filters['min_price'] ?? null, fn ($q, $min) => $q->where('price', '>=', $min)
        );

        $query->when($filters['max_price'] ?? null, fn ($q, $max) => $q->where('price', '<=', $max)
        );

        $query->when($filters['search'] ?? null, fn ($q, $search) => $q->search($search)
        );

        return $query;
    }
}
