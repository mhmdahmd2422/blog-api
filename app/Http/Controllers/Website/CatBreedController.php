<?php

namespace App\Http\Controllers\Website;

use App\Filters\Website\CatBreedFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Cats;

class CatBreedController extends Controller
{
    public function index(): Response
    {
        $paginationLength = pagination_length('catBreed');

        if ($breeds = Cats::breeds()->paginate($paginationLength)) {
            return response([
                'Breeds' => $breeds
            ]);
        }

        return response([
            'message' => __('third-party.error')
        ], 503);
    }
}
