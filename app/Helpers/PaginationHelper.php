<?php

if (! function_exists('pagination_length')) {
    function pagination_length(string $model): int
    {
        $length = match ($model) {
            'category' => 6,
            'post' => 6,
            'comment' => 12,
            'user' => 12,
            'tag' => 12,
            'specification' => 12,
            'place' => 12,
            'catBreed' => 12,
            'catFact' => 12,
        };

        return $length;
    }
}
