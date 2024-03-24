<?php

if (! function_exists('pagination_length')) {
    function pagination_length(string $model): int
    {
        $length = match ($model) {
            'category' => 6,
            'post' => 6,
            'comment' => 12,
            'user' => 12,
        };

        return $length;
    }
}
