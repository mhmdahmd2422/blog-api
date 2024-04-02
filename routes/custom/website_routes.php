<?php

use App\Http\Controllers\Website\Auth\LoginController;
use App\Http\Controllers\Website\Auth\LogoutController;
use App\Http\Controllers\Website\Auth\RegisterController;
use App\Http\Controllers\Website\CategoryController;
use App\Http\Controllers\Website\CommentController;
use App\Http\Controllers\Website\PlaceController;
use App\Http\Controllers\Website\PostController;
use App\Http\Controllers\Website\SpecificationController;
use App\Http\Controllers\Website\TagController;
use App\Http\Middleware\ResourceVisibility;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'website.auth.',
    'prefix' => 'auth'
], function () {
    Route::apiResource('login', LoginController::class)->only('store');
    Route::apiResource('register', RegisterController::class)->only('store');
    Route::apiResource('logout', LogoutController::class)
        ->only('store')->middleware('auth:api');
});

Route::name('website.')
    ->middleware(['auth:api', ResourceVisibility::class])
    ->group(function () {
    Route::apiResource('/posts', PostController::class)
        ->only(['index', 'show']);
    Route::apiResource('posts.comments', CommentController::class)->scoped();
    Route::apiResource('categories', CategoryController::class)
        ->only(['index', 'show']);
    Route::apiResource('tags', TagController::class)
        ->only(['index', 'show']);
    Route::apiResource('specifications', SpecificationController::class)
        ->only(['index', 'show']);
    Route::apiResource('places', PlaceController::class)
        ->only(['index', 'show']);
});
