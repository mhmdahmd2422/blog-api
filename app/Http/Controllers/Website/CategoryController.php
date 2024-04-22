<?php

namespace App\Http\Controllers\Website;

use App\Filters\Website\CategoryFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Website\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(CategoryFilter $filters): Response
    {
        $paginationLength = pagination_length('category');

        return response([
            'categories' => CategoryResource::collection(Category::visible()
                ->filter($filters)->get())->paginate($paginationLength)->withQueryString()
        ]);
    }

    public function show(Category $category): Response
    {
        return response([
            'category' => CategoryResource::make($category->load('image'))
        ]);
    }
}
