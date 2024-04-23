<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Services\CatsAPI\CatsService;
use Illuminate\Http\Response;

class CatFactController extends Controller
{

    public function index(CatsService $catsService): Response
    {
        $paginationLength = pagination_length('catFact');

        if ($facts = $catsService->allFacts($paginationLength)) {
            return response([
                'Facts' => $facts
            ]);
        }

        return response([
            'message' => __('third-party.error')
        ], 503);
    }
}
