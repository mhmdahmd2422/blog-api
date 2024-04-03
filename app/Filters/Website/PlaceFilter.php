<?php

namespace App\Filters\Website;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class PlaceFilter extends QueryFilter
{
    public function search($keyword = ''): Builder
    {
        return $this->builder->scopes(['search' => [['name', 'description'], $keyword]]);
    }

    public function order($order = 'asc'): Builder
    {
        return $this->builder->orderBy('created_at', $order);
    }
    public function tag($tag = ''): Builder
    {
        return $this->builder->whereHas('tags', function($q) use ($tag) {
            $q->where('name', 'like', "%{$tag}%");
        });
    }

    public function specification($specification = ''): Builder
    {
        return $this->builder->whereHas('specifications', function($q) use ($specification) {
            $q->where('name', 'like', "%{$specification}%");
        });
    }
}
