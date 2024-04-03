<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QueryFilter
{
    protected Builder $builder;

    public function __construct(protected Request $request)
    {
    }

    public function apply($builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                call_user_func_array(
                    [$this, $name],
                    array_filter([$value], function($v) {
                        return $v !== false && !is_null($v) && ($v != '' || $v == '0');
                    })
                );
            }
        }

        return $this->builder;
    }

    public function filters()
    {
        return $this->request->all();
    }
}
