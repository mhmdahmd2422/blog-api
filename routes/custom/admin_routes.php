<?php

use App\Http\Controllers\Admin\PostCommentController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'admin.',
    'prefix' => 'admin'
], function () {
    Route::resource('/users', UserController::class)
        ->except(['create', 'edit']);
    Route::resource('/posts', PostController::class)
        ->except(['create', 'edit']);
    Route::resource('posts.comments', PostCommentController::class)
        ->except(['create', 'edit'])->shallow();
});
