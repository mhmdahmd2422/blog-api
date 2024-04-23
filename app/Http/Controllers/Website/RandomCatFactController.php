<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Services\CatsAPI\CatsService;
use Illuminate\Http\Response;

class RandomCatFactController extends Controller
{
    public function index(CatsService $catsService): Response
    {
        if ($fact = $catsService->randomFact()) {
            return response([
                'Fact' => $fact
            ]);
        }

        return response([
            'message' => __('third-party.error')
        ], 503);
    }
}
