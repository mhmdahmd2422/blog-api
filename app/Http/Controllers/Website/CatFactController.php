<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Cats;

class CatFactController extends Controller
{

    public function index(): Response
    {
        $paginationLength = request('limit', pagination_length('catFact'));
        $facts = Cats::facts();

        if ($facts = $facts->paginate($paginationLength, $facts->count())->withQueryString()) {
            return response([
                'Facts' => $facts
            ]);
        }

        return response([
            'message' => __('third-party.error')
        ], 503);
    }
}
