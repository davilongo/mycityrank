<?php

use Illuminate\Support\Facades\Route;

// Posts públicos
Route::get('/posts', [App\Http\Controllers\PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [App\Http\Controllers\PostController::class, 'show'])->name('posts.show');

// Posts protegidos
Route::middleware('auth')->group(function () {
    Route::get('/posts/create', [App\Http\Controllers\PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [App\Http\Controllers\PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [App\Http\Controllers\PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [App\Http\Controllers\PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [App\Http\Controllers\PostController::class, 'destroy'])->name('posts.destroy');
});

Route::post('/posts/{post}/like', [\App\Http\Controllers\LikeController::class, 'toggle'])
    ->middleware('auth')
    ->name('posts.like');
Route::post('/posts/{post}/comment', [App\Http\Controllers\PostController::class, 'comment'])
    ->middleware('auth')
    ->name('posts.comment');


require __DIR__.'/auth.php';  // ← ESTA LÍNEA ES CLAVE
