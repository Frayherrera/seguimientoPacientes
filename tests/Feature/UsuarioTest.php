<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function datosUsuario(array $overrides = []): array
{
    return array_merge([
        'nombre' => 'Carlos',
        'apellidos' => 'García Martínez',
        'email' => 'cgarcia@clinicaaguachica.com',
        'role_id' => Role::create(['nombre' => 'Médico'])->id,
        'telefono' => '3002345678',
        'es_activo' => '1',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ], $overrides);
}

test('el listado de usuarios requiere autenticación', function () {
    $this->get(route('dashboard.usuarios'))->assertRedirect(route('login'));
});

test('el listado muestra los usuarios con su rol', function () {
    $rol = Role::create(['nombre' => 'Médico']);
    User::factory()->create(['nombre' => 'Patricia', 'role_id' => $rol->id]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.usuarios'))
        ->assertOk()
        ->assertSee('Patricia')
        ->assertSee('Médico');
});

test('el listado busca por nombre, apellidos o correo', function () {
    User::factory()->create(['nombre' => 'Ricardo', 'apellidos' => 'Mendoza Paz', 'email' => 'rmendoza@x.com']);
    User::factory()->create(['nombre' => 'Sofía', 'apellidos' => 'Lara Ruiz', 'email' => 'slara@x.com']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.usuarios', ['search' => 'Mendoza']))
        ->assertOk()
        ->assertSee('Ricardo')
        ->assertDontSee('Sofía');
});

test('se puede crear un usuario con contraseña hasheada', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.usuarios.store'), datosUsuario())
        ->assertRedirect(route('dashboard.usuarios'))
        ->assertSessionHas('success');

    $usuario = User::firstWhere('email', 'cgarcia@clinicaaguachica.com');

    expect($usuario)->not->toBeNull()
        ->and($usuario->nombre)->toBe('Carlos')
        ->and($usuario->es_activo)->toBeTrue()
        ->and(Hash::check('secret123', $usuario->password))->toBeTrue();
});

test('la creación valida campos, email único y rol existente', function () {
    User::factory()->create(['email' => 'ocupado@x.com']);
    $rol = Role::create(['nombre' => 'Enfermero']);

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.usuarios.store'), ['nombre' => 'Sin', 'password' => 'corto'])
        ->assertSessionHasErrors(['apellidos', 'email', 'role_id', 'password']);

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.usuarios.store'), datosUsuario([
            'email' => 'ocupado@x.com',
            'role_id' => $rol->id + 999,
        ]))
        ->assertSessionHasErrors(['email', 'role_id']);

    expect(User::count())->toBe(3); // 2 actores del test + 1 del factory inicial
});

test('al actualizar, la contraseña vacía mantiene la actual', function () {
    $usuario = User::factory()->create(['password' => Hash::make('original123')]);

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.usuarios.update', $usuario), datosUsuario([
            'nombre' => 'Actualizado',
            'password' => '',
            'password_confirmation' => '',
        ]))
        ->assertRedirect(route('dashboard.usuarios'))
        ->assertSessionHas('success');

    expect(Hash::check('original123', $usuario->fresh()->password))->toBeTrue()
        ->and($usuario->fresh()->nombre)->toBe('Actualizado');
});

test('al actualizar se puede cambiar la contraseña', function () {
    $usuario = User::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.usuarios.update', $usuario), datosUsuario([
            'password' => 'nuevaClave1',
            'password_confirmation' => 'nuevaClave1',
        ]))
        ->assertSessionHas('success');

    expect(Hash::check('nuevaClave1', $usuario->fresh()->password))->toBeTrue();
});

test('no se puede eliminar la propia cuenta', function () {
    $yo = User::factory()->create();

    $this->actingAs($yo)
        ->delete(route('dashboard.usuarios.destroy', $yo))
        ->assertRedirect(route('dashboard.usuarios'))
        ->assertSessionHas('error');

    expect(User::count())->toBe(1);
});

test('no se puede desactivar la propia cuenta', function () {
    $yo = User::factory()->create();

    $this->actingAs($yo)
        ->put(route('dashboard.usuarios.update', $yo), datosUsuario([
            'email' => $yo->email,
            'es_activo' => '0',
            'password' => '',
            'password_confirmation' => '',
        ]))
        ->assertSessionHas('error');

    expect($yo->fresh()->es_activo)->toBeTrue();
});

test('se puede eliminar otro usuario (soft delete)', function () {
    $victima = User::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('dashboard.usuarios.destroy', $victima))
        ->assertRedirect(route('dashboard.usuarios'))
        ->assertSessionHas('success');

    expect(User::count())->toBe(1)
        ->and(User::withTrashed()->find($victima->id))->not->toBeNull();
});
