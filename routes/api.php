<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EvidenciaApiController;
use App\Http\Controllers\Api\TrabajoApiController;
use App\Http\Controllers\Api\TrabajoDescripcionApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/trabajos/disponibles', [TrabajoApiController::class, 'disponibles']);
    Route::get('/trabajos/asignados', [TrabajoApiController::class, 'asignados']);
    Route::post('/trabajos/{trabajo}/asignar', [TrabajoApiController::class, 'asignar']);
    Route::post('/trabajos/{trabajo}/finalizar', [TrabajoApiController::class, 'finalizar']);
    Route::post('/trabajos/{trabajo}/abandonar', [TrabajoApiController::class, 'abandonar']);

    Route::get('/trabajos/{trabajo}/evidencias', [EvidenciaApiController::class, 'index']);
    Route::post('/trabajos/{trabajo}/evidencias', [EvidenciaApiController::class, 'store']);
    Route::put('/evidencias/{evidencia}', [EvidenciaApiController::class, 'update']);
    Route::delete('/evidencias/{evidencia}', [EvidenciaApiController::class, 'destroy']);

    Route::post('/evidencias/bulk-update', [EvidenciaApiController::class, 'bulkUpdate']);
    Route::post('/evidencias/bulk-delete', [EvidenciaApiController::class, 'bulkDelete']);

    Route::get('/trabajos/{trabajo}/descripciones', [TrabajoDescripcionApiController::class, 'index']);
    Route::post('/trabajos/{trabajo}/descripciones', [TrabajoDescripcionApiController::class, 'store']);
    Route::put('/descripciones/{descripcion}', [TrabajoDescripcionApiController::class, 'update']);
    Route::delete('/descripciones/{descripcion}', [TrabajoDescripcionApiController::class, 'destroy']);
});