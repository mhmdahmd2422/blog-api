<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Filters\Website\Services\CatFactFilter;
use Illuminate\Http\Response;
use Cats;

class CatFactController extends Controller
{
    public function index(CatFactFilter $filters): Response
    {
        $paginationLength = user_defined_pagination('limit', 'catFact');
        $facts = Cats::facts()->filter($filters)->get();

        if ($facts) {
            return response([
                'Facts' => $facts->paginate($paginationLength, $facts->count())->withQueryString()
            ]);
        }

        return response([
            'message' => __('third-party.error')
        ], 203);
    }
}
