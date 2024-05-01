<?php

use App\Http\Controllers\Website\Auth\ForgetPasswordController;
use App\Http\Controllers\Website\Auth\LoginController;
use App\Http\Controllers\Website\Auth\LogoutController;
use App\Http\Controllers\Website\Auth\RegisterController;
use App\Http\Controllers\Website\Auth\ResetPasswordController;
use App\Http\Controllers\Website\CatBreedController;
use App\Http\Controllers\Website\CategoryController;
use App\Http\Controllers\Website\CatFactController;
use App\Http\Controllers\Website\CommentController;
use App\Http\Controllers\Website\PlaceController;
use App\Http\Controllers\Website\PostController;
use App\Http\Controllers\Website\RandomCatFactController;
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
    Route::apiResource('password/forget', ForgetPasswordController::class)->only('store');
    Route::apiResource('password/reset', ResetPasswordController::class)->only('store');
});

Route::name('website.')
    ->middleware(['auth:api', 'set-locale', ResourceVisibility::class])
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
    }
);

Route::name('website.')
    ->group(function () {
       Route::apiResource('cats', CatBreedController::class)->only('index');
       Route::apiResource('facts', CatFactController::class)->only('index');
       Route::apiResource('fact', RandomCatFactController::class)->only('index');
    }
);
