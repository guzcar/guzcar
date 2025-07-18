<?php

use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EvidenciaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Pdf\DespachoController as PdfDespachoController;
use App\Http\Controllers\Pdf\TrabajoController as PdfTrabajoController;
use App\Http\Controllers\Pdf\VentaController as PdfVentaController;
use App\Http\Controllers\TrabajoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
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
    return redirect()->route('login');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->middleware('guest')->name('login');
    Route::post('/login', 'login')->middleware('guest')->name('login.process');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('password/reset', 'showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    Route::post('password/reset', 'reset')->name('password.update');
});

Route::middleware(['auth', '2fa.verified'])->group(function () {

    // Home principal
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Trabajos
    Route::get('/trabajos/asignar', [TrabajoController::class, 'asignarTrabajos'])->name('trabajos.asignar');
    Route::post('/trabajos/asignar/{trabajo}', [TrabajoController::class, 'asignar'])->name('trabajos.asignar.post');
    Route::post('/trabajos/finalizar/{trabajo}', [TrabajoController::class, 'finalizar'])->name('trabajos.finalizar');
    Route::delete('/trabajos/abandonar/{trabajo}', [TrabajoController::class, 'abandonar'])->name('trabajos.abandonar');

    // Articulos
    Route::get('/articulos', [ArticuloController::class, 'index'])->name('articulos');
    Route::get('/trabajos/{trabajo}/articulos', [ArticuloController::class, 'trabajo'])->name('gestion.trabajos.articulos');
    Route::post('/trabajos/{trabajoArticulo}/articulos-trabajo', [ArticuloController::class, 'confirmarTrabajo'])->name('gestion.trabajos.articulos.confirmar.trabajo');
    Route::post('/trabajos/{trabajoArticulo}/articulos-index', [ArticuloController::class, 'confirmarIndex'])->name('gestion.trabajos.articulos.confirmar.index');
    Route::post('/trabajos/{trabajo}/articulos-trabajo-todos', [ArticuloController::class, 'confirmarTrabajoTodos'])->name('gestion.trabajos.articulos.confirmar.trabajo.todos');
    Route::post('/trabajos/articulos-index-todos', [ArticuloController::class, 'confirmarIndexTodos'])->name('gestion.trabajos.articulos.confirmar.index.todos');

    // Consulta Vehicular
    Route::get('/consulta-vehicular', [VehiculoController::class, 'consultaVehicular'])->name('consulta.vehicular');
    Route::get('/consulta-vehicular/buscar', [VehiculoController::class, 'buscarVehiculo'])->name('consulta.buscar.vehiculo');
    Route::get('/vehiculo/{id}/articulos', [VehiculoController::class, 'articulosUtilizados'])->name('consulta.vehiculo.articulos');
    Route::get('/vehiculo/{id}/servicios', [VehiculoController::class, 'serviciosEjecutados'])->name('consulta.vehiculo.servicios');

    // PDF
    Route::get('/admin/trabajos/pdf/{trabajo}/presupuesto', [PdfTrabajoController::class, 'presupuesto'])->name('trabajo.pdf.presupuesto');
    Route::get('/admin/trabajos/pdf/{trabajo}/proforma', [PdfTrabajoController::class, 'proforma'])->name('trabajo.pdf.proforma');
    Route::get('/admin/trabajos/pdf/{trabajo}/informe', [PdfTrabajoController::class, 'informe'])->name('trabajo.pdf.informe');
    Route::get('/admin/evidencias/pdf/{trabajo}', [PdfTrabajoController::class, 'evidencia'])->name('trabajo.pdf.evidencia');
    Route::get('/admin/despachos/pdf/{despacho}', [PdfDespachoController::class, 'downloadPdf'])->name('despachos.pdf');
    Route::get('/admin/ventas/pdf/{venta}', [PdfVentaController::class, 'downloadPdf'])->name('ventas.pdf');

    // Evidencias
    Route::get('/trabajos/{trabajo}/evidencias', [EvidenciaController::class, 'index'])->name('gestion.evidencias.index');
    Route::get('/trabajos/{trabajo}/evidencias/todas', [EvidenciaController::class, 'all'])->name('gestion.evidencias.all');
    Route::post('/trabajos/{trabajo}/evidencias', [EvidenciaController::class, 'store'])->name('gestion.evidencias.store');
    Route::put('/trabajos/{trabajo}/evidencias/{evidencia}', [EvidenciaController::class, 'update'])->name('gestion.evidencias.update');
    Route::delete('/trabajos/{trabajo}/evidencias/{evidencia}', [EvidenciaController::class, 'destroy'])->name('gestion.evidencias.destroy');

    // Editar perfil
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/profile/edit', [UserController::class, 'update'])->name('user.update');
    Route::post('/profile/add-avatar', [UserController::class, 'addAvatar'])->name('user.add-avatar');
    Route::post('/profile/remove-avatar', [UserController::class, 'removeAvatar'])->name('user.remove-avatar');

    Route::get('/asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');
    Route::post('/asistencia', [AsistenciaController::class, 'registrar'])->name('asistencia.registrar');
});
