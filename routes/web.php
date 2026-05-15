<?php

use App\Http\Controllers\ProfileController;
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



// Dashboard routes
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {

    Route::view('/', 'dashboard.index')->name('index');
    Route::view('/usuarios', 'dashboard.usuarios')->name('usuarios');
    Route::view('/pacientes', 'dashboard.pacientes')->name('pacientes');
    Route::view('/enfermedades', 'dashboard.enfermedades')->name('enfermedades');
    Route::view('/seguimiento', 'dashboard.seguimiento')->name('seguimiento');
    Route::view('/historial', 'dashboard.historial')->name('historial');
    Route::view('/citas', 'dashboard.citas')->name('citas');
    Route::view('/reportes', 'dashboard.reportes')->name('reportes');
    Route::view('/configuracion', 'dashboard.configuracion')->name('configuracion');
});


// Ruta de búsqueda
Route::get('/buscar', function (\Illuminate\Http\Request $request) {
    $query = $request->get('q');
    return view('buscar', ['query' => $query]);
})->name('buscar')->middleware('auth');

// Ruta de logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');



require __DIR__ . '/auth.php';
