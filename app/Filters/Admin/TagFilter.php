<?php

namespace App\Filters\Admin;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class TagFilter extends QueryFilter
{
    public function visible($visibility = 1): Builder
    {
        return $this->builder->scopes(['visible' => [$visibility]]);
    }
    public function order($order = 'asc'): Builder
    {
        return $this->builder->orderBy('created_at', $order);
    }

    public function search($keyword = ''): Builder
    {
        return $this->builder->scopes(['search' => [['name'], $keyword]]);
    }

    public function place($place = ''): Builder
    {
        return $this->builder->whereHas('places', function($q) use ($place) {
            $q->where('name', 'like', "%{$place}%");
        });
    }
}
