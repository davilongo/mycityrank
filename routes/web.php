<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CiudadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotificationController;

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

// Perfiles de usuario (buscar y descubrir deben ir antes que {user})
Route::get('/users/buscar', [UserController::class, 'search'])->name('users.search');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

// Hashtags
Route::get('/hashtag/{name}', [PostController::class, 'hashtag'])->name('hashtag.show');

// Mapa
Route::get('/mapa', [PostController::class, 'map'])->name('mapa');

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

    // Bookmarks
    Route::get('/guardados', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/posts/{post}/bookmark', [BookmarkController::class, 'toggle'])->name('posts.bookmark');

    // Seguidores
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');
    Route::get('/feed', [FollowController::class, 'feed'])->name('feed');
    Route::get('/descubrir', [UserController::class, 'discover'])->name('users.discover');

    // Notificaciones
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notificaciones/{id}/leida', [NotificationController::class, 'markRead'])->name('notifications.read');
});

require __DIR__.'/auth.php';
