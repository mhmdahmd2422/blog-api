<?php

namespace App\Filters\Admin;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class UserFilter extends QueryFilter
{
    public function order($order = 'asc'): Builder
    {
        return $this->builder->orderBy('created_at', $order);
    }

    public function search($keyword = ''): Builder
    {
        return $this->builder->scopes(['search' => [['name'], $keyword]]);
    }

    public function activity($order = 'asc'): Builder
    {
        return $this->builder->withCount('comments')->orderBy('comments_count', $order);
    }
}
