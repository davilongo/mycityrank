<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CiudadController;
use Illuminate\Support\Facades\DB;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


Route::get('/', function () {
    return redirect()->route('posts.index'); // O mostrar vista de inicio
});
Route::get('/test-db', function () {
    return DB::select('SHOW DATABASES');
});

Route::resource('posts', PostController::class);

// Ruta pública para buscar ciudades (autocomplete)
Route::get('/ciudades/buscar', [CiudadController::class, 'buscar'])->name('ciudades.buscar');
