<?php

namespace App\Filters\Website\Services;

use App\Filters\CollectionFilter;

class CatFactFilter extends CollectionFilter
{
    public function fact($keyword = '')
    {
        return $this->collection->filter(function ($item) use ($keyword) {
            return false !== stristr($item['fact'] ?? null, $keyword);
        });
    }
    public function max_length($max_length = 500)
    {
        return $this->collection->where('length', '<=', $max_length);
    }
}
