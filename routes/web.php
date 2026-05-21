<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return redirect()->route('posts.index'); // Redirect to posts dashboard
});

Route::get('/posts/search', [PostController::class, 'search'])->name('posts.search');

Route::patch('/posts/{id}/toggle-status', [PostController::class, 'toggleStatus'])->name('posts.toggleStatus');

Route::resource('posts', PostController::class);