<?php

use App\Http\Controllers\Website\PostCommentController;
use App\Http\Controllers\Website\PostController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'website.',
], function () {
    Route::resource('/posts', PostController::class)
        ->except(['create', 'edit']);
    Route::resource('posts.comments', PostCommentController::class)
        ->except(['create', 'edit'])->shallow();
});
