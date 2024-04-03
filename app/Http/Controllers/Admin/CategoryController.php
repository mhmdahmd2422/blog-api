<?php

namespace App\Http\Controllers\Admin;

use App\Filters\Admin\CategoryFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Http\Resources\Admin\PostSimpleResource;
use App\Models\Category;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(CategoryFilter $filters): Response
    {
        $paginationLength = pagination_length('category');

        return response([
            'categories' => CategoryResource::collection(Category::with('image')->filter($filters)
                ->paginate($paginationLength))
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
            'category' => CategoryResource::make($category->load('image')),
            'posts' => PostSimpleResource::collection($category->posts)
                ->paginate(pagination_length('post'))
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): Response
    {
        $category = $request->updateCategory();

        return response([
            'category' => CategoryResource::make($category->load('image')),
            'message' => __('categories.update')
        ]);
    }

    public function destroy(Category $category): Response
    {
        if ($category->remove()) {
            return response([
                'message' => __('categories.destroy')
            ]);
        }

        return response([
            'message' => __('categories.cant_destroy'),
        ], 409);
    }
}
