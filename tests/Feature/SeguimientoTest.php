<?php

use App\Models\Alerta;
use App\Models\ConfiguracionAlerta;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\SignoVital;
use App\Models\TipoEnfermedad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function datosControl(array $overrides = []): array
{
    return array_merge([
        'paciente_id' => 0, // se rellena en cada test
        'fecha_atencion' => now()->toDateString(),
        'hora_atencion' => '10:30',
        'tipo_atencion' => 'Control',
        'motivo_consulta' => 'Control rutinario de seguimiento',
        'impresion_diagnostica' => 'Paciente estable, sin novedades',
        'plan_manipulacion' => 'Continuar con manejo habitual',
        'indicaciones' => 'Dieta y ejercicio',
        'estado' => 'En seguimiento',
        'presion_sistolica' => '120',
        'presion_diastolica' => '80',
        'frecuencia_cardiaca' => '72',
        'temperatura' => '36.5',
        'peso' => '80',
        'talla' => '170',
    ], $overrides);
}

function pacienteEnSeguimiento(array $overrides = []): Paciente
{
    $paciente = Paciente::factory()->create($overrides);

    HistoriaClinica::create([
        'paciente_id' => $paciente->id,
        'medico_id' => User::factory()->create()->id,
        'fecha_atencion' => now()->subDays(3)->toDateString(),
        'hora_atencion' => '09:00',
        'tipo_atencion' => 'Control',
        'motivo_consulta' => 'Control previo',
        'impresion_diagnostica' => 'Estable',
        'estado' => 'En seguimiento',
    ]);

    return $paciente;
}

test('el seguimiento requiere autenticación', function () {
    $this->get(route('dashboard.seguimiento'))->assertRedirect(route('login'));
});

test('el seguimiento lista solo pacientes activos con control o alertas', function () {
    $enSeguimiento = pacienteEnSeguimiento(['documento_identidad' => '11112222']); // tiene historia clínica
    $sinHistoria = Paciente::factory()->create(['documento_identidad' => '33334444']);
    $inactivo = pacienteEnSeguimiento(['documento_identidad' => '55556666', 'estado' => 'Inactivo']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.seguimiento'))
        ->assertOk()
        ->assertSee($enSeguimiento->nombre_completo)
        // el documento seguido de " •" solo aparece en las filas de la tabla,
        // no en las opciones del select del modal (que lista todos los activos)
        ->assertDontSee('33334444 •')
        ->assertDontSee($inactivo->nombre_completo);
});

test('las estadísticas del seguimiento se calculan con datos reales', function () {
    $paciente = pacienteEnSeguimiento();
    HistoriaClinica::create([
        'paciente_id' => $paciente->id,
        'medico_id' => User::factory()->create()->id,
        'fecha_atencion' => now()->toDateString(),
        'hora_atencion' => '08:00',
        'tipo_atencion' => 'Control',
        'motivo_consulta' => 'Control de hoy',
        'impresion_diagnostica' => 'Estable',
        'estado' => 'Abierta',
    ]);
    Alerta::create([
        'paciente_id' => $paciente->id,
        'tipo_alerta' => 'Valor fuera de rango',
        'parametro' => 'glucosa',
        'valor_detectado' => 250,
        'nivel_severidad' => 'Crítico',
        'mensaje' => 'Glucosa muy alta',
        'es_leida' => false,
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.seguimiento'))
        ->assertOk()
        ->assertSeeInOrder(['Controles Hoy', '1', 'Alertas sin Leer', '1', 'Críticas sin Leer', 'En Seguimiento']);
});

test('el filtro por estado usa las alertas sin leer del paciente', function () {
    $urgente = pacienteEnSeguimiento(['documento_identidad' => '11112222']);
    Alerta::create([
        'paciente_id' => $urgente->id,
        'tipo_alerta' => 'Valor fuera de rango',
        'parametro' => 'glucosa',
        'valor_detectado' => 310,
        'nivel_severidad' => 'Crítico',
        'mensaje' => 'Glucosa muy alta',
        'es_leida' => false,
    ]);

    $pendiente = pacienteEnSeguimiento(['documento_identidad' => '33334444']);
    Alerta::create([
        'paciente_id' => $pendiente->id,
        'tipo_alerta' => 'Valor fuera de rango',
        'parametro' => 'peso',
        'valor_detectado' => 120,
        'nivel_severidad' => 'Advertencia',
        'mensaje' => 'Peso fuera de rango',
        'es_leida' => false,
    ]);

    $alDia = pacienteEnSeguimiento(['documento_identidad' => '55556666']);

    // "documento •" solo aparece en filas de la tabla (el select del modal
    // muestra "nombre (documento)" sin el bullet)
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.seguimiento', ['estado' => 'Urgente']))
        ->assertOk()
        ->assertSee($urgente->nombre_completo)
        ->assertDontSee('33334444 •')
        ->assertDontSee('55556666 •');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.seguimiento', ['estado' => 'Pendiente']))
        ->assertOk()
        ->assertSee($pendiente->nombre_completo)
        ->assertDontSee('11112222 •')
        ->assertDontSee('55556666 •');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.seguimiento', ['estado' => 'Al día']))
        ->assertOk()
        ->assertSee($alDia->nombre_completo)
        ->assertDontSee('11112222 •')
        ->assertDontSee('33334444 •');
});

test('el seguimiento busca por nombre, apellido o documento', function () {
    $paciente = pacienteEnSeguimiento(['documento_identidad' => '99887766']);
    $otro = pacienteEnSeguimiento(['documento_identidad' => '77778888']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.seguimiento', ['search' => '99887766']))
        ->assertOk()
        ->assertSee($paciente->nombre_completo)
        ->assertDontSee('77778888 •');
});

test('se puede registrar un control con signos vitales', function () {
    $paciente = Paciente::factory()->create();
    $medico = User::factory()->create();

    $this->actingAs($medico)
        ->post(route('dashboard.seguimiento.store'), datosControl(['paciente_id' => $paciente->id]))
        ->assertRedirect(route('dashboard.seguimiento'))
        ->assertSessionHas('success');

    $historia = HistoriaClinica::firstWhere('paciente_id', $paciente->id);

    expect($historia)->not->toBeNull()
        ->and($historia->medico_id)->toBe($medico->id)
        ->and($historia->motivo_consulta)->toBe('Control rutinario de seguimiento')
        ->and($historia->estado)->toBe('En seguimiento');

    $signos = $historia->signosVitales()->first();

    expect($signos)->not->toBeNull()
        ->and((float) $signos->presion_sistolica)->toBe(120.0)
        ->and((float) $signos->peso)->toBe(80.0)
        ->and((float) $signos->imc)->toBeGreaterThan(27) // 80 / 1.7² ≈ 27.7
        ->and((float) $signos->imc)->toBeLessThan(28);
});

test('un control sin signos vitales no crea registro de signos', function () {
    $paciente = Paciente::factory()->create();

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.seguimiento.store'), datosControl([
            'paciente_id' => $paciente->id,
            'presion_sistolica' => null,
            'presion_diastolica' => null,
            'frecuencia_cardiaca' => null,
            'temperatura' => null,
            'peso' => null,
            'talla' => null,
        ]))
        ->assertRedirect(route('dashboard.seguimiento'));

    expect(HistoriaClinica::count())->toBe(1)
        ->and($paciente->signosVitales()->count())->toBe(0);
});

test('valores fuera de rango generan alertas por parámetro', function () {
    $paciente = Paciente::factory()->create();
    $diabetes = TipoEnfermedad::create(['nombre' => 'Diabetes Mellitus Tipo 2', 'codigo_cie10' => 'E11']);
    $paciente->enfermedades()->attach($diabetes->id, ['fecha_diagnostico' => now()->toDateString()]);

    ConfiguracionAlerta::create([
        'tipo_enfermedad_id' => $diabetes->id,
        'nombre_parametro' => 'glucosa',
        'valor_minimo' => 70,
        'valor_maximo' => 180,
        'unidad' => 'mg/dL',
        'nivel_alerta' => 'Medio',
        'mensaje_alerta' => 'Glucosa fuera de rango',
        'es_activo' => true,
    ]);
    ConfiguracionAlerta::create([
        'tipo_enfermedad_id' => $diabetes->id,
        'nombre_parametro' => 'glucosa',
        'valor_minimo' => 54,
        'valor_maximo' => 300,
        'unidad' => 'mg/dL',
        'nivel_alerta' => 'Alto',
        'mensaje_alerta' => 'Glucosa en rango crítico',
        'es_activo' => true,
    ]);
    ConfiguracionAlerta::create([
        'tipo_enfermedad_id' => $diabetes->id,
        'nombre_parametro' => 'peso',
        'valor_minimo' => 50,
        'valor_maximo' => 120,
        'unidad' => 'kg',
        'nivel_alerta' => 'Medio',
        'es_activo' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.seguimiento.store'), datosControl([
            'paciente_id' => $paciente->id,
            'glucosa' => '310', // fuera de Medio (70-180) y de Alto (54-300) → queda la más severa
            'peso' => '80', // dentro de rango
        ]))
        ->assertRedirect(route('dashboard.seguimiento'))
        ->assertSessionHas('success');

    $alerta = Alerta::first();

    expect(Alerta::count())->toBe(1)
        ->and($alerta->parametro)->toBe('glucosa')
        ->and((float) $alerta->valor_detectado)->toBe(310.0)
        ->and($alerta->nivel_severidad)->toBe('Crítico')
        ->and($alerta->es_leida)->toBeFalse()
        ->and($alerta->mensaje)->toBe('Glucosa en rango crítico');
});

test('valores dentro de rango no generan alertas', function () {
    $paciente = Paciente::factory()->create();
    $hipertension = TipoEnfermedad::create(['nombre' => 'Hipertensión Arterial']);
    $paciente->enfermedades()->attach($hipertension->id, ['fecha_diagnostico' => now()->toDateString()]);

    ConfiguracionAlerta::create([
        'tipo_enfermedad_id' => $hipertension->id,
        'nombre_parametro' => 'presion_sistolica',
        'valor_minimo' => 90,
        'valor_maximo' => 140,
        'unidad' => 'mmHg',
        'nivel_alerta' => 'Medio',
        'es_activo' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.seguimiento.store'), datosControl(['paciente_id' => $paciente->id]))
        ->assertRedirect(route('dashboard.seguimiento'))
        ->assertSessionHas('success', 'Control registrado para '.$paciente->nombre_completo.'.');

    expect(Alerta::count())->toBe(0);
});

test('editar un control regenera sus alertas sin duplicarlas', function () {
    $paciente = Paciente::factory()->create();
    $diabetes = TipoEnfermedad::create(['nombre' => 'Diabetes Mellitus Tipo 2']);
    $paciente->enfermedades()->attach($diabetes->id, ['fecha_diagnostico' => now()->toDateString()]);

    ConfiguracionAlerta::create([
        'tipo_enfermedad_id' => $diabetes->id,
        'nombre_parametro' => 'glucosa',
        'valor_minimo' => 70,
        'valor_maximo' => 180,
        'unidad' => 'mg/dL',
        'nivel_alerta' => 'Medio',
        'es_activo' => true,
    ]);

    $medico = User::factory()->create();

    $this->actingAs($medico)
        ->post(route('dashboard.seguimiento.store'), datosControl([
            'paciente_id' => $paciente->id,
            'glucosa' => '250',
        ]));

    $historia = HistoriaClinica::first();
    expect(Alerta::count())->toBe(1);

    $this->actingAs($medico)
        ->put(route('dashboard.seguimiento.update', $historia), datosControl([
            'paciente_id' => $paciente->id,
            'glucosa' => '100', // ahora dentro de rango
            'motivo_consulta' => 'Control corregido',
        ]))
        ->assertRedirect(route('dashboard.seguimiento'));

    expect(Alerta::count())->toBe(0)
        ->and($historia->fresh()->motivo_consulta)->toBe('Control corregido');
});

test('el paciente de una historia clínica es inmutable al editar', function () {
    $paciente = pacienteEnSeguimiento();
    $otro = Paciente::factory()->create();
    $historia = $paciente->historiasClinicas()->first();

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.seguimiento.update', $historia), datosControl([
            'paciente_id' => $otro->id,
        ]))
        ->assertRedirect(route('dashboard.seguimiento'));

    expect($historia->fresh()->paciente_id)->toBe($paciente->id);
});

test('valores de peso o talla fuera de rangos plausibles son rechazados', function () {
    $paciente = Paciente::factory()->create();

    // El caso que rompía en MySQL: talla = 1 cm producía imc = 10000
    // y la columna imc es decimal(5,2) (máx. 999.99)
    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.seguimiento.store'), datosControl([
            'paciente_id' => $paciente->id,
            'peso' => '1',
            'talla' => '1',
        ]))
        ->assertSessionHasErrors('talla');

    // Combinación válida por separado pero con IMC absurdo: 500 / 0.3² = 5555
    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.seguimiento.store'), datosControl([
            'paciente_id' => $paciente->id,
            'peso' => '500',
            'talla' => '30',
        ]))
        ->assertSessionHasErrors('talla');

    expect(HistoriaClinica::count())->toBe(0)
        ->and(SignoVital::count())->toBe(0);
});

test('el hook de IMC no asigna valores que excedan la columna decimal(5,2)', function () {
    $paciente = Paciente::factory()->create();
    $historia = HistoriaClinica::create([
        'paciente_id' => $paciente->id,
        'medico_id' => User::factory()->create()->id,
        'fecha_atencion' => now()->toDateString(),
        'hora_atencion' => '09:00',
        'tipo_atencion' => 'Control',
        'motivo_consulta' => 'Control',
        'impresion_diagnostica' => 'Estable',
        'estado' => 'Abierta',
    ]);

    $signo = $paciente->signosVitales()->create([
        'historia_clinica_id' => $historia->id,
        'peso' => 1,
        'talla' => 1, // imc = 10000 → fuera de decimal(5,2)
    ]);

    expect($signo->imc)->toBeNull();

    $signoNormal = $paciente->signosVitales()->create([
        'historia_clinica_id' => $historia->id,
        'peso' => 80,
        'talla' => 170,
    ]);

    expect((float) $signoNormal->imc)->toBeGreaterThan(27)
        ->and((float) $signoNormal->imc)->toBeLessThan(28);
});

test('la creación de controles valida los campos obligatorios y la fecha', function () {
    $this->actingAs(User::factory()->create())
        ->from(route('dashboard.seguimiento'))
        ->post(route('dashboard.seguimiento.store'), ['paciente_id' => ''])
        ->assertSessionHasErrors(['paciente_id', 'fecha_atencion', 'hora_atencion', 'motivo_consulta', 'impresion_diagnostica']);

    $paciente = Paciente::factory()->create();

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.seguimiento.store'), datosControl([
            'paciente_id' => $paciente->id,
            'fecha_atencion' => now()->addDay()->toDateString(),
        ]))
        ->assertSessionHasErrors('fecha_atencion');

    expect(HistoriaClinica::count())->toBe(0);
});

test('el detalle del paciente devuelve el último control y sus alertas', function () {
    $paciente = pacienteEnSeguimiento();
    Alerta::create([
        'paciente_id' => $paciente->id,
        'tipo_alerta' => 'Valor fuera de rango',
        'parametro' => 'glucosa',
        'valor_detectado' => 200,
        'nivel_severidad' => 'Advertencia',
        'mensaje' => 'Glucosa por encima del rango',
        'es_leida' => false,
    ]);

    $respuesta = $this->actingAs(User::factory()->create())
        ->getJson(route('dashboard.seguimiento.show', $paciente))
        ->assertOk()
        ->assertJsonPath('nombres', $paciente->nombres)
        ->assertJsonCount(1, 'alertas');

    $historia = $respuesta->json('historias_clinicas.0');
    expect($historia['id'])->toBe($paciente->historiasClinicas()->first()->id);
});

test('se puede marcar una alerta como leída', function () {
    $paciente = pacienteEnSeguimiento();
    $alerta = Alerta::create([
        'paciente_id' => $paciente->id,
        'tipo_alerta' => 'Valor fuera de rango',
        'parametro' => 'glucosa',
        'valor_detectado' => 200,
        'nivel_severidad' => 'Advertencia',
        'mensaje' => 'Glucosa por encima del rango',
        'es_leida' => false,
    ]);
    $medico = User::factory()->create();

    $this->actingAs($medico)
        ->put(route('dashboard.seguimiento.alertas.leer', $alerta))
        ->assertRedirect(route('dashboard.seguimiento'))
        ->assertSessionHas('success');

    $alerta->refresh();

    expect($alerta->es_leida)->toBeTrue()
        ->and($alerta->fecha_lectura)->not->toBeNull()
        ->and($alerta->usuario_lectura_id)->toBe($medico->id);
});
