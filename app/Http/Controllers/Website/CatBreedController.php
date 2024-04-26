<?php

namespace App\Http\Controllers\Website;

use App\Filters\Website\Services\CatBreedFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Cats;

class CatBreedController extends Controller
{
    public function index(CatBreedFilter $filters): Response
    {
        $paginationLength = user_defined_pagination('limit', 'catBreed');
        $breeds = Cats::breeds()->filter($filters)->get();

        if ($breeds) {
            return response([
                'Breeds' => $breeds->paginate($paginationLength)
            ]);
        }

        return response([
            'message' => __('third-party.error')
        ], 203);
    }
}
