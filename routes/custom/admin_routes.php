<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\admin\PostImageController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'admin.',
    'prefix' => 'admin'
], function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('posts.images', PostImageController::class)
        ->parameters(['images' => 'imageId'])->only(['store', 'update', 'destroy']);
    Route::apiResource('posts.comments', CommentController::class)
        ->except(['store', 'update'])->scoped();
    Route::apiResource('categories', CategoryController::class);
});
