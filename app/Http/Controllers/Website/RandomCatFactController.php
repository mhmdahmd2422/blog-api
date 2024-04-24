<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Cats;

class RandomCatFactController extends Controller
{
    public function index(): Response
    {
        if ($fact = Cats::randomFact()) {
            return response([
                'Fact' => $fact
            ]);
        }

        return response([
            'message' => __('third-party.error')
        ], 503);
    }
}
