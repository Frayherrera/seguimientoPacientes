<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/gestion', function () {
    return view('gestion');
})->name('gestion');

Route::view('/', 'clinica')->name('home');

// Rutas específicas
Route::view('/demo/solicitar', 'demo')->name('demo.solicitar');
Route::view('/contacto/ventas', 'contacto-ventas')->name('contacto.ventas');
Route::view('/login', 'login')->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
