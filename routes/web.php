<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', fn() => redirect()->route('posts.index'));

Route::get('/posts/search', [PostController::class, 'search'])->name('posts.search');
Route::patch('/posts/{id}/toggle-status', [PostController::class, 'toggleStatus'])->name('posts.toggleStatus');

Route::post('/presets', [PostController::class, 'savePreset'])->name('presets.save');
Route::delete('/presets/{id}', [PostController::class, 'deletePreset'])->name('presets.delete');

Route::resource('posts', PostController::class);
