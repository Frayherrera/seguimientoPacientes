<?php

use App\Models\Alerta;
use App\Models\Cita;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\TipoEnfermedad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function dashMedico(): User
{
    return User::factory()->create();
}

function dashCita(Paciente $paciente, User $medico, array $overrides = []): Cita
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

function dashHistoria(Paciente $paciente, User $medico, array $overrides = []): HistoriaClinica
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

function dashAlerta(Paciente $paciente, array $overrides = []): Alerta
{
    return Alerta::create(array_merge([
        'paciente_id' => $paciente->id,
        'tipo_alerta' => 'Valor fuera de rango',
        'parametro' => 'glucosa',
        'valor_detectado' => 250,
        'nivel_severidad' => 'Advertencia',
        'mensaje' => 'Glucosa fuera de rango',
        'es_leida' => false,
    ], $overrides));
}

test('el dashboard requiere autenticación', function () {
    $this->get(route('dashboard.index'))->assertRedirect(route('login'));
});

test('el dashboard muestra estadísticas reales', function () {
    Paciente::factory()->create(); // activo, registrado hoy
    Paciente::factory()->inactivo();

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.index'))
        ->assertOk()
        ->assertSee('Total Pacientes')
        ->assertSee('2 nuevos este mes')
        ->assertSee('Pacientes Activos')
        ->assertSee('50% del total')
        ->assertSee('Citas del Día')
        ->assertSee('Sin pendientes hoy')
        ->assertSee('Pacientes Críticos')
        ->assertSee('Sin alertas críticas sin leer');
});

test('las citas del día cuentan solo las de hoy', function () {
    $medico = dashMedico();
    $paciente = Paciente::factory()->create();

    dashCita($paciente, $medico);
    dashCita($paciente, $medico, [
        'fecha_cita' => now()->addDay()->toDateString(),
        'hora_inicio' => '11:00',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.index'))
        ->assertOk()
        ->assertSee('1 pendiente hoy');
});

test('los pacientes críticos son los que tienen alertas críticas sin leer', function () {
    $paciente = Paciente::factory()->create([
        'nombres' => 'Criticón',
        'apellidos' => 'de Prueba',
    ]);

    dashAlerta($paciente, ['nivel_severidad' => 'Crítico', 'mensaje' => 'Glucosa en emergencia']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.index'))
        ->assertOk()
        ->assertSee('Criticón de Prueba')
        ->assertSee('1 alerta crítica sin leer')
        ->assertSee('Alto'); // chip de riesgo derivado de la alerta

    // Al marcar la alerta como leída, el paciente deja de ser crítico
    Alerta::query()->update(['es_leida' => true]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.index'))
        ->assertSee('Sin alertas críticas sin leer')
        ->assertDontSee('Alto');
});

test('la actividad reciente refleja eventos reales', function () {
    $medico = dashMedico();
    $paciente = Paciente::factory()->create(['nombres' => 'María', 'apellidos' => 'García López']);

    dashCita($paciente, $medico);
    dashHistoria($paciente, $medico);
    dashAlerta($paciente);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.index'))
        ->assertOk()
        ->assertSee('Nuevo paciente registrado: María García López')
        ->assertSee('Cita programada para María García López')
        ->assertSee('Alerta Advertencia: María García López')
        ->assertSee('Control registrado para María García López');
});

test('la tabla de últimos registros muestra paciente, enfermedad y último control reales', function () {
    $medico = dashMedico();
    $tipo = TipoEnfermedad::create(['nombre' => 'Diabetes Mellitus Tipo 2']);
    $paciente = Paciente::factory()->create(['nombres' => 'Pedro', 'apellidos' => 'Gómez Ruiz']);

    $paciente->enfermedades()->attach($tipo->id, [
        'fecha_diagnostico' => now()->toDateString(),
        'estado' => 'Activo',
    ]);
    dashHistoria($paciente, $medico);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.index'))
        ->assertOk()
        ->assertSeeInOrder(['Pedro Gómez Ruiz', 'Diabetes Mellitus Tipo 2', 'Activo', now()->format('d/m/Y'), 'Bajo']);
});

test('la tabla de últimos registros muestra el estado vacío sin pacientes', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.index'))
        ->assertOk()
        ->assertSee('No hay pacientes registrados todavía')
        ->assertSee('Todavía no hay actividad registrada');
});

test('los gráficos se alimentan con datos reales', function () {
    $medico = dashMedico();
    $tipo = TipoEnfermedad::create(['nombre' => 'EPOC Grave']);
    $paciente = Paciente::factory()->create();

    $paciente->enfermedades()->attach($tipo->id, [
        'fecha_diagnostico' => now()->toDateString(),
        'estado' => 'Activo',
    ]);
    dashHistoria($paciente, $medico, ['tipo_atencion' => 'Urgencias']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.index'))
        ->assertOk()
        // JSON del gráfico de barras (prevalencia) y del gráfico de pastel (tipos de atención)
        ->assertSee('EPOC Grave', false)
        ->assertSee('Urgencias', false);
});
