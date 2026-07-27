<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CiudadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CiudadFollowController;
use App\Http\Controllers\ViajeController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

// Cambio de idioma
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['es', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

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

// Viajes organizados (create antes que {viaje} para evitar colisión)
Route::get('/viajes', [ViajeController::class, 'index'])->name('viajes.index');
Route::get('/viajes/create', [ViajeController::class, 'create'])->middleware(['auth', 'agencia'])->name('viajes.create');
Route::get('/viajes/{viaje}', [ViajeController::class, 'show'])->name('viajes.show');
Route::get('/viajes/{viaje}/edit', [ViajeController::class, 'edit'])->middleware(['auth', 'agencia'])->name('viajes.edit');

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
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->middleware('throttle:30,1')->name('posts.like');
    Route::post('/posts/{post}/comment', [PostController::class, 'comment'])->middleware('throttle:30,1')->name('posts.comment');

    // Perfil (editar)
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

    // Bookmarks
    Route::get('/guardados', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/posts/{post}/bookmark', [BookmarkController::class, 'toggle'])->middleware('throttle:30,1')->name('posts.bookmark');

    // Seguir ciudades
    Route::post('/ciudades/{ciudad}/follow', [CiudadFollowController::class, 'toggle'])->middleware('throttle:30,1')->name('ciudades.follow');

    // Seguidores
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->middleware('throttle:30,1')->name('users.follow');
    Route::get('/feed', [FollowController::class, 'feed'])->name('feed');
    Route::get('/descubrir', [UserController::class, 'discover'])->name('users.discover');

    // Notificaciones
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notificaciones/{id}/leida', [NotificationController::class, 'markRead'])->name('notifications.read');
});

// Viajes (solo agencia o admin)
Route::middleware(['auth', 'agencia'])->group(function () {
    Route::post('/viajes', [ViajeController::class, 'store'])->name('viajes.store');
    Route::put('/viajes/{viaje}', [ViajeController::class, 'update'])->name('viajes.update');
    Route::delete('/viajes/{viaje}', [ViajeController::class, 'destroy'])->name('viajes.destroy');
});

// Admin: gestión de agencias (crear antes que {user} para evitar colisión)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios.index');
    Route::get('/usuarios/nueva-agencia', [AdminController::class, 'crearAgencia'])->name('usuarios.create-agencia');
    Route::post('/usuarios/nueva-agencia', [AdminController::class, 'storeAgencia'])->name('usuarios.store-agencia');
    Route::post('/usuarios/{user}/agencia', [AdminController::class, 'toggleAgencia'])->name('usuarios.toggle-agencia');
});

require __DIR__.'/auth.php';
