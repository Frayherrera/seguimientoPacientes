<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

// No hay registro público: las cuentas las crea un administrador desde el módulo Usuarios.

test('no existe la ruta de registro', function () {
    expect(Route::has('register'))->toBeFalse();

    $this->get('/register')->assertNotFound();
});

test('no se puede crear una cuenta por POST a /register', function () {
    $this->post('/register', [
        'nombre' => 'Intruso',
        'email' => 'intruso@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
    expect(User::where('email', 'intruso@example.com')->exists())->toBeFalse();
});

test('el login no ofrece enlace de registro', function () {
    $this->get(route('login'))
        ->assertDontSee('Regístrate')
        ->assertDontSee('/register');
});
