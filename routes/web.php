<?php

use App\Http\Controllers\CitaController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnfermedadController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'clinica')->name('home');

// Rutas específicas
Route::view('/demo/solicitar', 'demo')->name('demo.solicitar');
Route::view('/contacto/ventas', 'contacto-ventas')->name('contacto.ventas');
Route::view('/login', 'login')->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Ruta de búsqueda
Route::get('/buscar', function (Request $request) {
    $query = $request->get('q');

    return view('buscar', ['query' => $query]);
})->name('buscar')->middleware('auth');

// Ruta de logout
Route::post('/logout', function () {
    Auth::logout();

    return redirect('/');
})->name('logout');

// Dashboard routes
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    Route::get('/pacientes', [PacienteController::class, 'index'])->name('pacientes');
    Route::post('/pacientes', [PacienteController::class, 'store'])->name('pacientes.store');
    Route::get('/pacientes/{paciente}', [PacienteController::class, 'show'])->name('pacientes.show');
    Route::put('/pacientes/{paciente}', [PacienteController::class, 'update'])->name('pacientes.update');
    Route::delete('/pacientes/{paciente}', [PacienteController::class, 'destroy'])->name('pacientes.destroy');
    Route::get('/enfermedades', [EnfermedadController::class, 'index'])->name('enfermedades');
    Route::post('/enfermedades', [EnfermedadController::class, 'store'])->name('enfermedades.store');
    Route::get('/enfermedades/{enfermedad}', [EnfermedadController::class, 'show'])->name('enfermedades.show');
    Route::put('/enfermedades/{enfermedad}', [EnfermedadController::class, 'update'])->name('enfermedades.update');
    Route::delete('/enfermedades/{enfermedad}', [EnfermedadController::class, 'destroy'])->name('enfermedades.destroy');
    Route::get('/seguimiento', [SeguimientoController::class, 'index'])->name('seguimiento');
    Route::post('/seguimiento', [SeguimientoController::class, 'store'])->name('seguimiento.store');
    Route::get('/seguimiento/pacientes/{paciente}', [SeguimientoController::class, 'show'])->name('seguimiento.show');
    Route::put('/seguimiento/controles/{control}', [SeguimientoController::class, 'update'])->name('seguimiento.update');
    Route::put('/seguimiento/alertas/{alerta}/leer', [SeguimientoController::class, 'marcarLeida'])->name('seguimiento.alertas.leer');
    Route::get('/historial', [HistorialController::class, 'index'])->name('historial');
    Route::get('/historial/export', [HistorialController::class, 'export'])->name('historial.export');
    Route::get('/historial/{historial}', [HistorialController::class, 'show'])->name('historial.show');
    Route::get('/citas', [CitaController::class, 'index'])->name('citas');
    Route::post('/citas', [CitaController::class, 'store'])->name('citas.store');
    Route::put('/citas/{cita}', [CitaController::class, 'update'])->name('citas.update');
    Route::put('/citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('citas.estado');
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes');
    Route::get('/reportes/export', [ReporteController::class, 'export'])->name('reportes.export');

    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');
    Route::put('/configuracion/perfil', [ConfiguracionController::class, 'actualizarPerfil'])->name('configuracion.perfil');
    Route::post('/configuracion/cerrar-sesiones', [ConfiguracionController::class, 'cerrarSesiones'])->name('configuracion.sesiones');
    Route::post('/configuracion/umbrales', [ConfiguracionController::class, 'guardarUmbral'])->name('configuracion.umbrales.store');
    Route::put('/configuracion/umbrales/{configuracionAlerta}', [ConfiguracionController::class, 'actualizarUmbral'])->name('configuracion.umbrales.update');
    Route::put('/configuracion/umbrales/{configuracionAlerta}/estado', [ConfiguracionController::class, 'cambiarEstadoUmbral'])->name('configuracion.umbrales.estado');
    Route::delete('/configuracion/umbrales/{configuracionAlerta}', [ConfiguracionController::class, 'eliminarUmbral'])->name('configuracion.umbrales.destroy');
});

require __DIR__.'/auth.php';
