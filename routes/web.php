<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CiudadController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

// Posts (create debe ir antes que {post})
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'])->middleware('auth')->name('posts.create');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Ciudades (buscar debe ir antes que {ciudad})
Route::get('/ciudades/buscar', [CiudadController::class, 'buscar'])->name('ciudades.buscar');
Route::get('/ciudades/{ciudad}', [CiudadController::class, 'show'])->name('ciudades.show');

// Perfiles de usuario
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

// Rutas protegidas (requieren login)
Route::middleware('auth')->group(function () {
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::post('/posts/{post}/comment', [PostController::class, 'comment'])->name('posts.comment');

    // Perfil (editar)
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
