<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Services\CatsAPI\CatsService;
use Illuminate\Http\Response;

class CatBreedController extends Controller
{
    public function index(CatsService $catsService): Response
    {
        $paginationLength = pagination_length('catBreed');

        if ($breeds = $catsService->allBreeds($paginationLength)) {
            return response([
                'Breeds' => $breeds
            ]);
        }

        return response([
            'message' => __('third-party.error')
        ], 503);
    }
}
