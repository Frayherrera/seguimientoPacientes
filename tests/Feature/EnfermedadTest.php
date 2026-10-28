<?php

use App\Models\Paciente;
use App\Models\TipoEnfermedad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function datosEnfermedad(array $overrides = []): array
{
    return array_merge([
        'nombre' => 'Diabetes Mellitus Tipo 2',
        'codigo_cie10' => 'E11',
        'descripcion' => 'Enfermedad crónica que afecta el procesamiento de la glucosa.',
        'es_activo' => '1',
    ], $overrides);
}

test('el catálogo de enfermedades requiere autenticación', function () {
    $this->get(route('dashboard.enfermedades'))->assertRedirect(route('login'));
});

test('el catálogo muestra las enfermedades registradas', function () {
    TipoEnfermedad::create(['nombre' => 'Hipertensión Arterial']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.enfermedades'))
        ->assertOk()
        ->assertSee('Hipertensión Arterial');
});

test('el catálogo busca por nombre o código CIE-10', function () {
    TipoEnfermedad::create(['nombre' => 'EPOC (Enfermedad Pulmonar)', 'codigo_cie10' => 'J44']);
    TipoEnfermedad::create(['nombre' => 'Diabetes Mellitus Tipo 2', 'codigo_cie10' => 'E11']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.enfermedades', ['search' => 'E11']))
        ->assertOk()
        ->assertSee('Diabetes')
        ->assertDontSee('Pulmonar');
});

test('se puede crear una enfermedad con parámetros de control', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.enfermedades.store'), datosEnfermedad([
            'parametros' => [
                ['nombre' => 'Glucosa', 'min' => '70', 'max' => '180', 'unidad' => 'mg/dL'],
                ['nombre' => '', 'min' => '', 'max' => '', 'unidad' => ''], // fila vacía del formulario
            ],
        ]))
        ->assertRedirect(route('dashboard.enfermedades'))
        ->assertSessionHas('success');

    $enfermedad = TipoEnfermedad::firstWhere('nombre', 'Diabetes Mellitus Tipo 2');

    expect($enfermedad)->not->toBeNull()
        ->and($enfermedad->codigo_cie10)->toBe('E11')
        ->and($enfermedad->es_activo)->toBeTrue()
        ->and($enfermedad->parametros_control)->toBe([
            'Glucosa' => ['min' => 70, 'max' => 180, 'unidad' => 'mg/dL'],
        ]);
});

test('la creación valida los campos obligatorios y el nombre único', function () {
    TipoEnfermedad::create(['nombre' => 'EPOC']);

    $this->actingAs(User::factory()->create())
        ->from(route('dashboard.enfermedades'))
        ->post(route('dashboard.enfermedades.store'), ['nombre' => ''])
        ->assertSessionHasErrors(['nombre']);

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.enfermedades.store'), datosEnfermedad(['nombre' => 'EPOC']))
        ->assertSessionHasErrors('nombre');

    expect(TipoEnfermedad::count())->toBe(1);
});

test('la fila de parámetro sin nombre no es válida', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.enfermedades.store'), datosEnfermedad([
            'parametros' => [
                ['nombre' => '', 'min' => '70', 'max' => '180', 'unidad' => 'mg/dL'],
            ],
        ]))
        ->assertSessionHasErrors('parametros.0.nombre');

    expect(TipoEnfermedad::count())->toBe(0);
});

test('se puede actualizar una enfermedad sin romper su propio nombre', function () {
    $enfermedad = TipoEnfermedad::create(['nombre' => 'Diabetes Mellitus Tipo 2']);

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.enfermedades.update', $enfermedad), datosEnfermedad([
            'descripcion' => 'Descripción actualizada.',
            'es_activo' => '0',
        ]))
        ->assertRedirect(route('dashboard.enfermedades'))
        ->assertSessionHas('success');

    expect($enfermedad->fresh()->descripcion)->toBe('Descripción actualizada.')
        ->and($enfermedad->fresh()->es_activo)->toBeFalse();
});

test('no se puede eliminar una enfermedad con pacientes asignados', function () {
    $enfermedad = TipoEnfermedad::create(['nombre' => 'Hipertensión Arterial']);
    $paciente = Paciente::factory()->create();
    $enfermedad->pacientes()->attach($paciente->id, [
        'fecha_diagnostico' => '2026-01-10',
        'estado' => 'Activo',
        'es_principal' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->delete(route('dashboard.enfermedades.destroy', $enfermedad))
        ->assertRedirect(route('dashboard.enfermedades'))
        ->assertSessionHas('error');

    expect(TipoEnfermedad::count())->toBe(1);
});

test('se puede eliminar una enfermedad sin pacientes (soft delete)', function () {
    $enfermedad = TipoEnfermedad::create(['nombre' => 'Artritis Reumatoide']);

    $this->actingAs(User::factory()->create())
        ->delete(route('dashboard.enfermedades.destroy', $enfermedad))
        ->assertRedirect(route('dashboard.enfermedades'))
        ->assertSessionHas('success');

    expect(TipoEnfermedad::count())->toBe(0)
        ->and(TipoEnfermedad::withTrashed()->find($enfermedad->id))->not->toBeNull();
});

test('el detalle de la enfermedad se devuelve en JSON con el conteo de pacientes', function () {
    $enfermedad = TipoEnfermedad::create(['nombre' => 'EPOC']);
    $paciente = Paciente::factory()->create();
    $enfermedad->pacientes()->attach($paciente->id, [
        'fecha_diagnostico' => '2026-01-10',
        'estado' => 'Activo',
        'es_principal' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.enfermedades.show', $enfermedad))
        ->assertOk()
        ->assertJsonFragment(['nombre' => 'EPOC'])
        ->assertJsonFragment(['pacientes_count' => 1]);
});
