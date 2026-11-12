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

Route::middleware(['auth', 'habilitado'])->group(function () {
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

// Dashboard routes: requieren sesión con usuario y rol activos; cada módulo
// exige su permiso (matriz en config/permisos.php)
Route::middleware(['auth', 'habilitado'])->prefix('dashboard')->name('dashboard.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::middleware('can:usuarios.gestionar')->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });

    Route::get('/pacientes', [PacienteController::class, 'index'])->name('pacientes')->middleware('can:pacientes.ver');
    Route::post('/pacientes', [PacienteController::class, 'store'])->name('pacientes.store')->middleware('can:pacientes.gestionar');
    Route::get('/pacientes/{paciente}', [PacienteController::class, 'show'])->name('pacientes.show')->middleware('can:pacientes.ver');
    Route::put('/pacientes/{paciente}', [PacienteController::class, 'update'])->name('pacientes.update')->middleware('can:pacientes.gestionar');
    Route::delete('/pacientes/{paciente}', [PacienteController::class, 'destroy'])->name('pacientes.destroy')->middleware('can:pacientes.eliminar');

    Route::get('/enfermedades', [EnfermedadController::class, 'index'])->name('enfermedades')->middleware('can:enfermedades.ver');
    Route::post('/enfermedades', [EnfermedadController::class, 'store'])->name('enfermedades.store')->middleware('can:enfermedades.gestionar');
    Route::get('/enfermedades/{enfermedad}', [EnfermedadController::class, 'show'])->name('enfermedades.show')->middleware('can:enfermedades.ver');
    Route::put('/enfermedades/{enfermedad}', [EnfermedadController::class, 'update'])->name('enfermedades.update')->middleware('can:enfermedades.gestionar');
    Route::delete('/enfermedades/{enfermedad}', [EnfermedadController::class, 'destroy'])->name('enfermedades.destroy')->middleware('can:enfermedades.gestionar');

    Route::get('/seguimiento', [SeguimientoController::class, 'index'])->name('seguimiento')->middleware('can:seguimiento.ver');
    Route::post('/seguimiento', [SeguimientoController::class, 'store'])->name('seguimiento.store')->middleware('can:seguimiento.registrar');
    Route::get('/seguimiento/pacientes/{paciente}', [SeguimientoController::class, 'show'])->name('seguimiento.show')->middleware('can:seguimiento.ver');
    Route::put('/seguimiento/controles/{control}', [SeguimientoController::class, 'update'])->name('seguimiento.update')->middleware('can:seguimiento.registrar');
    Route::put('/seguimiento/alertas/{alerta}/leer', [SeguimientoController::class, 'marcarLeida'])->name('seguimiento.alertas.leer')->middleware('can:alertas.atender');

    Route::middleware('can:historial.ver')->group(function () {
        Route::get('/historial', [HistorialController::class, 'index'])->name('historial');
        Route::get('/historial/export', [HistorialController::class, 'export'])->name('historial.export');
        Route::get('/historial/{historial}', [HistorialController::class, 'show'])->name('historial.show');
    });

    Route::get('/citas', [CitaController::class, 'index'])->name('citas')->middleware('can:citas.ver');
    Route::post('/citas', [CitaController::class, 'store'])->name('citas.store')->middleware('can:citas.gestionar');
    Route::put('/citas/{cita}', [CitaController::class, 'update'])->name('citas.update')->middleware('can:citas.gestionar');
    Route::put('/citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('citas.estado')->middleware('can:citas.gestionar');

    Route::middleware('can:reportes.ver')->group(function () {
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes');
        Route::get('/reportes/export', [ReporteController::class, 'export'])->name('reportes.export');
    });

    // Configuración: perfil propio y sesiones para todos; umbrales solo con permiso
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');
    Route::put('/configuracion/perfil', [ConfiguracionController::class, 'actualizarPerfil'])->name('configuracion.perfil');
    Route::post('/configuracion/cerrar-sesiones', [ConfiguracionController::class, 'cerrarSesiones'])->name('configuracion.sesiones');
    Route::middleware('can:umbrales.gestionar')->group(function () {
        Route::post('/configuracion/umbrales', [ConfiguracionController::class, 'guardarUmbral'])->name('configuracion.umbrales.store');
        Route::put('/configuracion/umbrales/{configuracionAlerta}', [ConfiguracionController::class, 'actualizarUmbral'])->name('configuracion.umbrales.update');
        Route::put('/configuracion/umbrales/{configuracionAlerta}/estado', [ConfiguracionController::class, 'cambiarEstadoUmbral'])->name('configuracion.umbrales.estado');
        Route::delete('/configuracion/umbrales/{configuracionAlerta}', [ConfiguracionController::class, 'eliminarUmbral'])->name('configuracion.umbrales.destroy');
    });
});

require __DIR__.'/auth.php';
