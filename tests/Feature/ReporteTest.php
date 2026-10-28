<?php

use App\Models\Alerta;
use App\Models\Cita;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\TipoEnfermedad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function reporteCita(Paciente $paciente, User $medico, array $overrides = []): Cita
{
    return Cita::create(array_merge([
        'paciente_id' => $paciente->id,
        'medico_id' => $medico->id,
        'fecha_cita' => now()->toDateString(),
        'hora_inicio' => '09:00',
        'tipo_cita' => 'Control',
        'motivo' => 'Control de rutina',
        'estado' => 'Programada',
    ], $overrides));
}

function reporteHistoria(Paciente $paciente, User $medico, array $overrides = []): HistoriaClinica
{
    return HistoriaClinica::create(array_merge([
        'paciente_id' => $paciente->id,
        'medico_id' => $medico->id,
        'fecha_atencion' => now()->toDateString(),
        'hora_atencion' => '10:00',
        'tipo_atencion' => 'Control',
        'motivo_consulta' => 'Control rutinario',
        'impresion_diagnostica' => 'Diabetes mellitus tipo 2 controlada',
        'estado' => 'Cerrada',
    ], $overrides));
}

test('los reportes requieren autenticación', function () {
    $this->get(route('dashboard.reportes'))->assertRedirect(route('login'));
    $this->get(route('dashboard.reportes.export'))->assertRedirect(route('login'));
});

test('el índice muestra estadísticas y gráficos con datos reales', function () {
    $medico = User::factory()->create();
    $paciente = Paciente::factory()->create();

    reporteCita($paciente, $medico, ['estado' => 'Atendida']);
    reporteCita($paciente, $medico, ['hora_inicio' => '11:00']);
    reporteHistoria($paciente, $medico);
    Alerta::create([
        'paciente_id' => $paciente->id,
        'historia_clinica_id' => $paciente->historiasClinicas()->first()->id,
        'tipo_alerta' => 'Valor fuera de rango',
        'parametro' => 'glucosa',
        'valor_detectado' => 250,
        'nivel_severidad' => 'Crítico',
        'mensaje' => 'Glucosa en rango de emergencia',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes'))
        ->assertOk()
        ->assertSee('Del '.now()->startOfMonth()->format('d/m/Y').' al '.now()->format('d/m/Y'))
        ->assertSee('Pacientes por Mes')
        ->assertSee('Distribución de Citas')
        ->assertSee('Pacientes por Estado y Género')
        ->assertSee('Citas por Tipo y Atención')
        ->assertSee('Prevalencia de Enfermedades')
        ->assertSee('Seguimiento y Alertas')
        ->assertSee('Crítico');
});

test('el filtro personalizado delimita el período en índice y exportación', function () {
    $anhoViejo = now()->subYears(3);

    Paciente::factory()->create([
        'nombres' => 'Paciente',
        'apellidos' => 'Bicentenario',
        'created_at' => $anhoViejo->copy()->startOfYear(),
    ]);
    Paciente::factory()->create([
        'nombres' => 'Paciente',
        'apellidos' => 'Reciente',
    ]);

    $filtros = [
        'periodo' => 'personalizado',
        'desde' => $anhoViejo->copy()->startOfYear()->toDateString(),
        'hasta' => $anhoViejo->copy()->endOfYear()->toDateString(),
    ];

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes', $filtros))
        ->assertOk()
        ->assertSee('Del '.$anhoViejo->copy()->startOfYear()->format('d/m/Y').' al '.$anhoViejo->copy()->endOfYear()->format('d/m/Y'));

    $contenido = $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes.export', array_merge(['tipo' => 'pacientes'], $filtros)))
        ->streamedContent();

    expect($contenido)
        ->toContain('Bicentenario')
        ->not->toContain('Reciente');
});

test('la validación del período rechaza valores inválidos', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes', ['periodo' => 'eternidades']))
        ->assertSessionHasErrors('periodo');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes', ['periodo' => 'personalizado']))
        ->assertSessionHasErrors(['desde', 'hasta']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes', [
            'periodo' => 'personalizado',
            'desde' => '2024-12-31',
            'hasta' => '2024-01-01',
        ]))
        ->assertSessionHasErrors('hasta');
});

test('la exportación resumen incluye las cuatro secciones con datos reales', function () {
    $medico = User::factory()->create();
    $paciente = Paciente::factory()->create();

    reporteCita($paciente, $medico, ['estado' => 'Atendida']);

    $contenido = $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes.export'))
        ->streamedContent();

    expect($contenido)
        ->toContain('Reporte general')
        ->toContain('Pacientes')
        ->toContain('Nuevos en el período')
        ->toContain('Citas')
        ->toContain('Atendida;1')
        ->toContain('Tasa de asistencia')
        ->toContain('Enfermedades')
        ->toContain('Seguimiento');
});

test('la exportación de citas respeta el período', function () {
    $medico = User::factory()->create();
    $paciente = Paciente::factory()->create();

    reporteCita($paciente, $medico, [
        'fecha_cita' => '2023-06-15',
        'motivo' => 'Cita del pasado',
    ]);

    $contenidoPasado = $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes.export', [
            'tipo' => 'citas',
            'periodo' => 'personalizado',
            'desde' => '2023-01-01',
            'hasta' => '2023-12-31',
        ]))
        ->streamedContent();

    expect($contenidoPasado)->toContain('Cita del pasado');

    $contenidoMes = $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes.export', ['tipo' => 'citas']))
        ->streamedContent();

    expect($contenidoMes)->not->toContain('Cita del pasado');
});

test('la exportación de seguimiento incluye los controles del período', function () {
    $medico = User::factory()->create();
    $paciente = Paciente::factory()->create();

    reporteHistoria($paciente, $medico, [
        'impresion_diagnostica' => 'Hipertensión arterial mal controlada',
    ]);

    $contenido = $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes.export', ['tipo' => 'seguimiento']))
        ->streamedContent();

    expect($contenido)->toContain('Hipertensión arterial mal controlada');
});

test('la prevalencia de enfermedades usa los diagnósticos reales', function () {
    $tipo = TipoEnfermedad::create([
        'nombre' => 'Diabetes mellitus tipo 2',
        'codigo_cie10' => 'E11',
    ]);

    $paciente = Paciente::factory()->create();
    $paciente->enfermedades()->attach($tipo->id, [
        'fecha_diagnostico' => now()->toDateString(),
        'estado' => 'Activo',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes'))
        ->assertOk()
        ->assertSee('Diabetes mellitus tipo 2')
        ->assertSee('E11');
});

test('la exportación de enfermedades lista la prevalencia', function () {
    $tipo = TipoEnfermedad::create(['nombre' => 'Hipertensión Arterial', 'codigo_cie10' => 'I10']);

    $paciente = Paciente::factory()->create();
    $paciente->enfermedades()->attach($tipo->id, [
        'fecha_diagnostico' => now()->toDateString(),
        'estado' => 'Activo',
    ]);

    $contenido = $this->actingAs(User::factory()->create())
        ->get(route('dashboard.reportes.export', ['tipo' => 'enfermedades']))
        ->streamedContent();

    expect($contenido)
        ->toContain('Hipertensión Arterial')
        ->toContain('I10');
});
