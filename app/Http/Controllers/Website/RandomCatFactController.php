<?php

namespace App\Http\Controllers\Website;

use App\Filters\Website\Services\CatFactFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Cats;

class RandomCatFactController extends Controller
{
    public function index(CatFactFilter $filters): Response
    {
        $fact = Cats::randomFact()->filter($filters)->random();

        if ($fact) {
            return response([
                'Fact' => $fact
            ]);
        }

        return response([
            'message' => __('third-party.error')
        ], 203);
    }
}
