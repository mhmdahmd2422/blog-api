<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        return response([
            'categories' => CategoryResource::collection(Category::all())
        ]);
    }

    public function store(StoreCategoryRequest $request): Response
    {
        $category = $request->storeCategory();

        return response([
            'category' => CategoryResource::make($category->load('image')),
            'message' => __('categories.store')
        ]);
    }

    public function show(Category $category): Response
    {
        return response([
            'category' => CategoryResource::make($category->load('image', 'posts')),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): Response
    {
        $request->updateCategory();

        return response([
            'category' => CategoryResource::make($category->fresh()),
            'message' => __('categories.update')
        ]);
    }

    public function destroy(Category $category): Response
    {
        $category->remove();

        return response([
            'message' => __('categories.destroy')
        ]);
    }
}
