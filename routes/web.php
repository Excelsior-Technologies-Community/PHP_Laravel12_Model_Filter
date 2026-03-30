<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return redirect()->route('posts.index'); // Redirect to posts dashboard
});

// ✅ Resource route handles index, create, store, edit, update, destroy
Route::resource('posts', PostController::class);