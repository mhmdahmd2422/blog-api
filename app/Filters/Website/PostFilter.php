<?php

namespace App\Filters\Website;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class PostFilter extends QueryFilter
{
    public function order($order = 'asc'): Builder
    {
        return $this->builder->orderBy('created_at', $order);
    }

    public function search($keyword = ''): Builder
    {
        return $this->builder->scopes(['search' => [['title', 'description'], $keyword, true]]);
    }

    public function author($author = ''): Builder
    {
        return $this->builder->whereHas('user', function($q) use($author) {
            $q->where('name', 'like', "%{$author}%");
        });
    }
}
