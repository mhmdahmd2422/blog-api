<?php

namespace App\Filters\Website;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class CommentFilter extends QueryFilter
{
    public function order($order = 'asc'): Builder
    {
        return $this->builder->orderBy('created_at', $order);
    }
}
