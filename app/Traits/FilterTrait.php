<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterTrait
{
    /**
     * Scope tìm kiếm
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $keyword
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }

        $fields = $this->searchable ?? [];

        return $query->where(function ($q) use ($keyword, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%$keyword%");
            }
        });
    }

    /**
     * Scope sắp xếp theo column
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @return Builder
     */
    public function scopeLasted(Builder $query, string $column = 'id'): Builder
    {
        return $query->orderByDesc($column);
    }

    /**
     * Scope lọc trạng thái người dùng
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $status
     * @return Builder
     */
    public function scopeStatus(Builder $query, mixed $status): Builder
    {
        if ($status === null || $status === '') {
            return $query;
        }

        if (defined(static::class . '::STATUS_ENUM')) {
            $enum = constant(static::class . '::STATUS_ENUM');
            if ($status instanceof $enum) {
                $status = $status->value;
            }
        }

        return $query->where('status', $status);
    }
}
