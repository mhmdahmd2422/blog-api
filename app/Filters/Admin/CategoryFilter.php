<?php

namespace App\Filters\Admin;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class CategoryFilter extends QueryFilter
{
    public function visible($visibility = 1): Builder
    {
        return $this->builder->scopes(['visible' => [$visibility]]);
    }

    public function search($keyword = ''): Builder
    {
        return $this->builder->scopes(['search' => [['name'], $keyword]]);
    }

    public function popular($order = 'asc'): Builder
    {
        return $this->builder->withCount('posts')->orderBy('posts_count', $order);
    }
}
