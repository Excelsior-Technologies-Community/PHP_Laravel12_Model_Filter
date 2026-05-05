<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return redirect()->route('posts.index'); // Redirect to posts dashboard
});

// ✅ FIRST: search route mukvo
Route::get('/posts/search', [PostController::class, 'search'])->name('posts.search');

// ✅ THEN resource routes
Route::resource('posts', PostController::class)->except(['show']);