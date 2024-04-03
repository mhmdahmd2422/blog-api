<?php

namespace App\Filters\Website;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class CategoryFilter extends QueryFilter
{
    public function search($keyword = ''): Builder
    {
        return $this->builder->scopes(['search' => [['name'], $keyword]]);
    }

    public function popular($order = 'asc'): Builder
    {
        return $this->builder->withCount('posts')->orderBy('posts_count', $order);
    }
}
