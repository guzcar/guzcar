<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EvidenciaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrabajoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->middleware('guest')->name('login');
    Route::post('/login', 'login')->middleware('guest')->name('login.process');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware(['auth'])->group(function () {

    // Home principal
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Trabajos
    Route::get('/trabajos/asignar', [TrabajoController::class, 'asignarTrabajos'])->name('trabajos.asignar');
    Route::post('/trabajos/asignar/{trabajo}', [TrabajoController::class, 'asignar'])->name('trabajos.asignar.post');
    Route::delete('/trabajos/abandonar/{trabajo}', [TrabajoController::class, 'abandonar'])->name('trabajos.abandonar');

    // Evidencias
    Route::get('/trabajos/{trabajo}/evidencias', [EvidenciaController::class, 'index'])->name('evidencias.index');
    Route::post('/trabajos/{trabajo}/evidencias', [EvidenciaController::class, 'store'])->name('evidencias.store');
    Route::put('/trabajos/{trabajo}/evidencias/{evidencia}', [EvidenciaController::class, 'update'])->name('evidencias.update');
    Route::delete('/trabajos/{trabajo}/evidencias/{evidencia}', [EvidenciaController::class, 'destroy'])->name('evidencias.destroy');

    // Editar perfil
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/profile/edit', [UserController::class, 'update'])->name('user.update');
    Route::post('/profile/remove-avatar', [UserController::class, 'removeAvatar'])->name('user.remove-avatar');
});
