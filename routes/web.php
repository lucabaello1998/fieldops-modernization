<?php

use App\Http\Controllers\AltaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\TecnicoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/buscar', [SearchController::class, 'index'])->name('buscar');

    Route::get('/tareas', [TareaController::class, 'index'])->name('tareas.index');
    Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
    Route::get('/tareas/{tarea}', [TareaController::class, 'show'])->name('tareas.show');
    Route::patch('/tareas/{tarea}/estado', [TareaController::class, 'updateEstado'])->name('tareas.estado');
    Route::delete('/tareas/{tarea}', [TareaController::class, 'destroy'])->name('tareas.destroy');
    Route::post('/tareas/{tarea}/nota', [TareaController::class, 'comentar'])->name('tareas.comentar');

    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::post('/stock/movimiento', [StockController::class, 'movimiento'])->name('stock.movimiento');

    Route::get('/altas', [AltaController::class, 'index'])->name('altas.index');
    Route::get('/altas/{alta}', [AltaController::class, 'show'])->name('altas.show');

    Route::get('/tecnicos', [TecnicoController::class, 'index'])->name('tecnicos.index');
    Route::get('/tecnicos/{tecnico}', [TecnicoController::class, 'show'])->name('tecnicos.show');

    Route::middleware('role:Administrador')->prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::post('/', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::patch('/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });
});
