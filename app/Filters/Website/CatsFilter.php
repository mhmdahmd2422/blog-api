<?php

namespace App\Filters\Website;

use App\Filters\CollectionFilter;

class CatsFilter extends CollectionFilter
{
    public function breed($keyword = '')
    {
        return $this->collection->filter(function ($item) use ($keyword) {
            return false !== stristr($item['breed'] ?? null, $keyword);
        });
    }

    public function country($keyword = '')
    {
        return $this->collection->filter(function ($item) use ($keyword) {
            return false !== stristr($item['country']  ?? null, $keyword);
        });
    }

    public function origin($keyword = '')
    {
        return $this->collection->filter(function ($item) use ($keyword) {
            return false !== stristr($item['origin']  ?? null, $keyword);
        });
    }

    public function coat($keyword = '')
    {
        return $this->collection->filter(function ($item) use ($keyword) {
            return false !== stristr($item['coat']  ?? null, $keyword);
        });
    }

    public function pattern($keyword = '')
    {
        return $this->collection->filter(function ($item) use ($keyword) {
            return false !== stristr($item['pattern']  ?? null, $keyword);
        });
    }
    public function max_length($max_length)
    {
        return $this->collection->where('length', '<=', $max_length);
    }
}
