<?php

namespace App\Filters;

use Illuminate\Support\Collection;

abstract class CollectionFilter
{
    protected Collection $collection;

    public function applyFilters($collection): Collection
    {
        $this->collection = $collection;

        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                $this->collection = call_user_func_array(
                    [$this, $name],
                    array_filter([$value])
                );
            }
        }


        return $this->collection;
    }

    public function filters()
    {
        return request()->all();
    }
}
