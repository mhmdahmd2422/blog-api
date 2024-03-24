<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ResourceVisibility;
use App\Http\Resources\Website\CategoryResource;
use App\Http\Resources\Website\PostSimpleResource;
use App\Models\Category;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(ResourceVisibility::class);
    }

    public function index(): Response
    {
        $paginationLength = pagination_length('category');

        return response([
            'categories' => CategoryResource::collection(Category::visible()->get())
                ->paginate($paginationLength)
        ]);
    }

    public function show(Category $category): Response
    {
        return response([
            'category' => CategoryResource::make($category->load('image')),
            'posts' => PostSimpleResource::collection($category->posts)
                ->paginate(pagination_length('post'))
        ]);
    }
}
