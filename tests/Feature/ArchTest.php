<?php

it('finds missing debug statements', function () {
    expect(['dd', 'dump', 'var_dump', 'print_r', 'echo', 'die', 'exit', 'error_log'])
        ->not->toBeUsed();
});

