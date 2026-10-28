<?php

use App\Models\ConfiguracionAlerta;
use App\Models\TipoEnfermedad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function configTipoEnfermedad(array $overrides = []): TipoEnfermedad
{
    return TipoEnfermedad::create(array_merge([
        'nombre' => 'Diabetes Mellitus Tipo 2',
        'codigo_cie10' => 'E11',
        'es_activo' => true,
    ], $overrides));
}

function configUmbral(TipoEnfermedad $tipo, array $overrides = []): ConfiguracionAlerta
{
    return ConfiguracionAlerta::create(array_merge([
        'tipo_enfermedad_id' => $tipo->id,
        'nombre_parametro' => 'glucosa',
        'valor_minimo' => 70,
        'valor_maximo' => 180,
        'unidad' => 'mg/dL',
        'nivel_alerta' => 'Medio',
        'es_activo' => true,
    ], $overrides));
}

function configDatosUmbral(TipoEnfermedad $tipo, array $overrides = []): array
{
    return array_merge([
        'tipo_enfermedad_id' => $tipo->id,
        'nombre_parametro' => 'glucosa',
        'valor_minimo' => 70,
        'valor_maximo' => 180,
        'unidad' => 'mg/dL',
        'nivel_alerta' => 'Medio',
        'es_activo' => 1,
    ], $overrides);
}

test('la configuración requiere autenticación', function () {
    $this->get(route('dashboard.configuracion'))->assertRedirect(route('login'));
});

test('el índice muestra los umbrales reales y el perfil del usuario', function () {
    $tipo = configTipoEnfermedad();
    configUmbral($tipo, ['mensaje_alerta' => 'Glucosa fuera de rango para diabéticos']);

    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->get(route('dashboard.configuracion'))
        ->assertOk()
        ->assertSee('Umbrales de Alerta Clínicas')
        ->assertSee('Diabetes Mellitus Tipo 2')
        ->assertSee('Glucosa')
        ->assertSee('Glucosa fuera de rango para diabéticos')
        ->assertSee($usuario->email);
});

test('el filtro por enfermedad muestra solo sus umbrales', function () {
    $diabetes = configTipoEnfermedad();
    $hipertension = configTipoEnfermedad(['nombre' => 'Hipertensión Arterial', 'codigo_cie10' => 'I10']);

    configUmbral($diabetes, ['mensaje_alerta' => 'Glucosa fuera de rango para diabéticos']);
    configUmbral($hipertension, [
        'nombre_parametro' => 'presion_sistolica',
        'unidad' => 'mmHg',
        'mensaje_alerta' => 'Presión sistólica elevada',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.configuracion', ['enfermedad_id' => $diabetes->id]))
        ->assertOk()
        ->assertSee('Glucosa fuera de rango para diabéticos')
        ->assertDontSee('Presión sistólica elevada');
});

test('se puede crear un umbral de alerta', function () {
    $tipo = configTipoEnfermedad();

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.configuracion.umbrales.store'), configDatosUmbral($tipo))
        ->assertRedirect(route('dashboard.configuracion'))
        ->assertSessionHas('success');

    $umbral = ConfiguracionAlerta::first();

    expect($umbral)->not->toBeNull()
        ->and($umbral->nombre_parametro)->toBe('glucosa')
        ->and($umbral->nivel_alerta)->toBe('Medio')
        ->and($umbral->es_activo)->toBeTrue();
});

test('no se permite duplicar umbral para el mismo parámetro y nivel', function () {
    $tipo = configTipoEnfermedad();
    configUmbral($tipo);

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.configuracion.umbrales.store'), configDatosUmbral($tipo))
        ->assertSessionHasErrors('nivel_alerta');

    // Un nivel distinto para el mismo parámetro sí está permitido
    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.configuracion.umbrales.store'), configDatosUmbral($tipo, ['nivel_alerta' => 'Alto']))
        ->assertSessionHasNoErrors();

    expect(ConfiguracionAlerta::count())->toBe(2);
});

test('el umbral valida el rango y la lista de parámetros', function () {
    $tipo = configTipoEnfermedad();

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.configuracion.umbrales.store'), configDatosUmbral($tipo, [
            'valor_minimo' => 200,
            'valor_maximo' => 100,
        ]))
        ->assertSessionHasErrors('valor_maximo');

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.configuracion.umbrales.store'), configDatosUmbral($tipo, [
            'nombre_parametro' => 'presion_arterial_inventada',
        ]))
        ->assertSessionHasErrors('nombre_parametro');

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.configuracion.umbrales.store'), configDatosUmbral($tipo, [
            'tipo_enfermedad_id' => 999999,
        ]))
        ->assertSessionHasErrors('tipo_enfermedad_id');

    expect(ConfiguracionAlerta::count())->toBe(0);
});

test('se puede editar un umbral', function () {
    $tipo = configTipoEnfermedad();
    $umbral = configUmbral($tipo);

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.configuracion.umbrales.update', $umbral), configDatosUmbral($tipo, [
            'valor_maximo' => 200,
            'nivel_alerta' => 'Alto',
        ]))
        ->assertRedirect(route('dashboard.configuracion'))
        ->assertSessionHas('success');

    $umbral->refresh();

    expect((float) $umbral->valor_maximo)->toBe(200.0)
        ->and($umbral->nivel_alerta)->toBe('Alto');
});

test('al editar se respeta la unicidad contra otros umbrales', function () {
    $tipo = configTipoEnfermedad();
    $umbralMedio = configUmbral($tipo);
    configUmbral($tipo, ['nivel_alerta' => 'Alto']);

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.configuracion.umbrales.update', $umbralMedio), configDatosUmbral($tipo, [
            'nivel_alerta' => 'Alto',
        ]))
        ->assertSessionHasErrors('nivel_alerta');

    expect($umbralMedio->fresh()->nivel_alerta)->toBe('Medio');
});

test('se puede activar y desactivar un umbral', function () {
    $tipo = configTipoEnfermedad();
    $umbral = configUmbral($tipo);

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.configuracion.umbrales.estado', $umbral))
        ->assertSessionHas('success');
    expect($umbral->fresh()->es_activo)->toBeFalse();

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.configuracion.umbrales.estado', $umbral))
        ->assertSessionHas('success');
    expect($umbral->fresh()->es_activo)->toBeTrue();
});

test('se puede eliminar un umbral', function () {
    $tipo = configTipoEnfermedad();
    $umbral = configUmbral($tipo);

    $this->actingAs(User::factory()->create())
        ->delete(route('dashboard.configuracion.umbrales.destroy', $umbral))
        ->assertRedirect(route('dashboard.configuracion'))
        ->assertSessionHas('success');

    expect(ConfiguracionAlerta::count())->toBe(0);
});

test('el usuario puede actualizar su perfil', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->put(route('dashboard.configuracion.perfil'), [
            'nombre' => 'María',
            'apellidos' => 'Pérez López',
            'email' => 'maria.perez@clinicaaguachica.com',
            'telefono' => '3001112233',
            'especialidad' => 'Pediatría',
            'registro_medico' => 'RM-12345',
        ])
        ->assertRedirect(route('dashboard.configuracion'))
        ->assertSessionHas('success');

    $usuario->refresh();

    expect($usuario->nombre)->toBe('María')
        ->and($usuario->email)->toBe('maria.perez@clinicaaguachica.com')
        ->and($usuario->especialidad)->toBe('Pediatría');
});

test('el email del perfil debe ser único', function () {
    $usuario = User::factory()->create();
    $otro = User::factory()->create();

    $this->actingAs($usuario)
        ->put(route('dashboard.configuracion.perfil'), [
            'nombre' => $usuario->nombre,
            'apellidos' => $usuario->apellidos,
            'email' => $otro->email,
        ])
        ->assertSessionHasErrors('email');
});

test('cambiar la contraseña exige la contraseña actual', function () {
    $usuario = User::factory()->create(); // contraseña: "password"

    $datosBase = [
        'nombre' => $usuario->nombre,
        'apellidos' => $usuario->apellidos,
        'email' => $usuario->email,
    ];

    // Contraseña actual incorrecta → rechazado, no cambia
    $this->actingAs($usuario)
        ->put(route('dashboard.configuracion.perfil'), array_merge($datosBase, [
            'password' => 'nueva12345',
            'password_confirmation' => 'nueva12345',
            'password_actual' => 'esta-no-es',
        ]))
        ->assertSessionHasErrors('password_actual');
    expect(Hash::check('password', $usuario->fresh()->password))->toBeTrue();

    // Sin contraseña actual → rechazado
    $this->actingAs($usuario)
        ->put(route('dashboard.configuracion.perfil'), array_merge($datosBase, [
            'password' => 'nueva12345',
            'password_confirmation' => 'nueva12345',
        ]))
        ->assertSessionHasErrors('password_actual');

    // Contraseña actual correcta → cambia
    $this->actingAs($usuario)
        ->put(route('dashboard.configuracion.perfil'), array_merge($datosBase, [
            'password' => 'nueva12345',
            'password_confirmation' => 'nueva12345',
            'password_actual' => 'password',
        ]))
        ->assertSessionHas('success');
    expect(Hash::check('nueva12345', $usuario->fresh()->password))->toBeTrue();

    // Sin nueva contraseña → mantiene la actual
    $this->actingAs($usuario)
        ->put(route('dashboard.configuracion.perfil'), array_merge($datosBase, [
            'password' => null,
        ]))
        ->assertSessionHas('success');
    expect(Hash::check('nueva12345', $usuario->fresh()->password))->toBeTrue();
});

test('se puede cerrar la sesión en otros dispositivos con la contraseña actual', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->post(route('dashboard.configuracion.sesiones'), ['password_actual' => 'password'])
        ->assertRedirect(route('dashboard.configuracion'))
        ->assertSessionHas('success');

    $this->actingAs($usuario)
        ->post(route('dashboard.configuracion.sesiones'), ['password_actual' => 'incorrecta'])
        ->assertSessionHasErrors('password_actual');
});
