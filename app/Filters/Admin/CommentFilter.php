<?php

namespace App\Filters\Admin;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class CommentFilter extends QueryFilter
{
    public function banned($banned = 1): Builder
    {
        return $this->builder->scopes(['isBanned' => [$banned]]);
    }

    public function order($order = 'asc'): Builder
    {
        return $this->builder->orderBy('created_at', $order);
    }
}
