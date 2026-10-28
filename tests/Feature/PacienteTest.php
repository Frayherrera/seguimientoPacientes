<?php

use App\Models\Paciente;
use App\Models\TipoEnfermedad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function datosPaciente(array $overrides = []): array
{
    return array_merge([
        'documento_identidad' => '1234567890',
        'tipo_documento' => 'CC',
        'nombres' => 'Juan',
        'apellidos' => 'Pérez López',
        'fecha_nacimiento' => '1980-05-15',
        'genero' => 'M',
        'telefono' => '2345678',
        'celular' => '3001234567',
        'email' => 'juan@email.com',
        'direccion' => 'Calle 1 # 2-3',
        'ciudad' => 'Aguachica',
        'departamento' => 'Cesar',
        'eps' => 'Sura',
        'ips_primaria' => 'IPS Aguachica',
        'estado' => 'Activo',
        'observaciones' => 'Paciente con control regular',
    ], $overrides);
}

test('el listado de pacientes requiere autenticación', function () {
    $this->get(route('dashboard.pacientes'))->assertRedirect(route('login'));
});

test('el listado muestra los pacientes registrados', function () {
    Paciente::factory()->create(['apellidos' => 'Zapata Ruiz', 'nombres' => 'Carla']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.pacientes'))
        ->assertOk()
        ->assertSee('Carla')
        ->assertSee('Zapata Ruiz');
});

test('el listado filtra por estado y búsqueda', function () {
    Paciente::factory()->create(['nombres' => 'Pedro', 'estado' => 'Activo']);
    Paciente::factory()->create(['nombres' => 'Lucia', 'estado' => 'Inactivo']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.pacientes', ['estado' => 'Inactivo']))
        ->assertOk()
        ->assertSee('Lucia')
        ->assertDontSee('Pedro');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.pacientes', ['search' => 'Pedro']))
        ->assertOk()
        ->assertSee('Pedro')
        ->assertDontSee('Lucia');
});

test('se puede registrar un paciente con enfermedad diagnósticada', function () {
    $enfermedad = TipoEnfermedad::create(['nombre' => 'Diabetes Mellitus Tipo 2']);

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.pacientes.store'), datosPaciente([
            'enfermedad_id' => $enfermedad->id,
            'fecha_diagnostico' => '2026-01-10',
        ]))
        ->assertRedirect(route('dashboard.pacientes'))
        ->assertSessionHas('success');

    $paciente = Paciente::firstWhere('documento_identidad', '1234567890');

    expect($paciente)->not->toBeNull()
        ->and($paciente->nombres)->toBe('Juan')
        ->and($paciente->enfermedades)->toHaveCount(1)
        ->and($paciente->enfermedades->first()->pivot->es_principal)->toBeTruthy();
});

test('el registro valida los campos obligatorios', function () {
    $this->actingAs(User::factory()->create())
        ->from(route('dashboard.pacientes'))
        ->post(route('dashboard.pacientes.store'), ['nombres' => 'Solo'])
        ->assertRedirect(route('dashboard.pacientes'))
        ->assertSessionHasErrors(['documento_identidad', 'apellidos', 'fecha_nacimiento', 'genero', 'estado']);

    expect(Paciente::count())->toBe(0);
});

test('no se puede registrar un documento duplicado', function () {
    Paciente::factory()->create(['documento_identidad' => '99999999']);

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.pacientes.store'), datosPaciente(['documento_identidad' => '99999999']))
        ->assertSessionHasErrors('documento_identidad');

    expect(Paciente::count())->toBe(1);
});

test('se puede actualizar un paciente sin romper su propio documento', function () {
    $paciente = Paciente::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.pacientes.update', $paciente), datosPaciente([
            'documento_identidad' => $paciente->documento_identidad,
            'nombres' => 'María',
            'apellidos' => 'García Soto',
            'estado' => 'Inactivo',
        ]))
        ->assertRedirect(route('dashboard.pacientes'))
        ->assertSessionHas('success');

    expect($paciente->fresh()->nombres)->toBe('María')
        ->and($paciente->fresh()->estado)->toBe('Inactivo');
});

test('al actualizar se puede agregar una enfermedad sin duplicar la pivot', function () {
    $paciente = Paciente::factory()->create();
    $enfermedad = TipoEnfermedad::create(['nombre' => 'Hipertensión Arterial']);
    $paciente->enfermedades()->attach($enfermedad->id, [
        'fecha_diagnostico' => '2026-02-01',
        'estado' => 'Activo',
        'es_principal' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.pacientes.update', $paciente), datosPaciente([
            'documento_identidad' => $paciente->documento_identidad,
            'enfermedad_id' => $enfermedad->id,
        ]))
        ->assertRedirect(route('dashboard.pacientes'));

    expect(DB::table('paciente_enfermedades')->where('paciente_id', $paciente->id)->count())->toBe(1);
});

test('se puede eliminar un paciente (soft delete)', function () {
    $paciente = Paciente::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('dashboard.pacientes.destroy', $paciente))
        ->assertRedirect(route('dashboard.pacientes'))
        ->assertSessionHas('success');

    expect(Paciente::count())->toBe(0)
        ->and(Paciente::withTrashed()->find($paciente->id))->not->toBeNull();
});

test('el detalle del paciente se devuelve en JSON', function () {
    $paciente = Paciente::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.pacientes.show', $paciente))
        ->assertOk()
        ->assertJsonFragment(['documento_identidad' => $paciente->documento_identidad]);
});
