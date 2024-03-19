<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ResourceVisibility;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(ResourceVisibility::class);
    }

    public function index()
    {
        return response([
            'categories' => CategoryResource::collection(Category::visible()->get())
        ]);
    }

    public function show(Category $category)
    {
        return response([
            'category' => CategoryResource::make($category->load('image')),
        ]);
    }
}
