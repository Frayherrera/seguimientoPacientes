<?php

use App\Models\Cita;
use App\Models\HistoriaClinica;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\Prescripcion;
use App\Models\SignoVital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function crearHistoria(Paciente $paciente, User $medico, array $overrides = []): HistoriaClinica
{
    return HistoriaClinica::create(array_merge([
        'paciente_id' => $paciente->id,
        'medico_id' => $medico->id,
        'fecha_atencion' => now()->toDateString(),
        'hora_atencion' => '10:00',
        'tipo_atencion' => 'Control',
        'motivo_consulta' => 'Control rutinario',
        'impresion_diagnostica' => 'Diabetes mellitus tipo 2 controlada',
        'plan_manipulacion' => 'Continuar con metformina',
        'estado' => 'Cerrada',
    ], $overrides));
}

test('el historial requiere autenticación', function () {
    $this->get(route('dashboard.historial'))->assertRedirect(route('login'));
});

test('el historial muestra los registros con sus estadísticas', function () {
    $medico = User::factory()->create();
    $paciente = Paciente::factory()->create();
    $otroPaciente = Paciente::factory()->create();

    crearHistoria($paciente, $medico, ['impresion_diagnostica' => 'Hipertensión arterial esencial']);
    crearHistoria($otroPaciente, $medico, [
        'fecha_atencion' => now()->subDays(40)->toDateString(),
        'impresion_diagnostica' => 'Diabetes mellitus tipo 2',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.historial'))
        ->assertOk()
        ->assertSee('Hipertensión arterial esencial')
        ->assertSee('Diabetes mellitus tipo 2')
        ->assertSee($paciente->nombre_completo)
        ->assertSee('Total Registros')
        ->assertSee('2'); // total de registros
});

test('la búsqueda cubre paciente, diagnóstico y médico', function () {
    $drLopez = User::factory()->create(['nombre' => 'Carlos', 'apellidos' => 'López']);
    $drPerez = User::factory()->create(['nombre' => 'Juan', 'apellidos' => 'Pérez']);
    $paciente = Paciente::factory()->create(['documento_identidad' => '1234567890']);

    crearHistoria($paciente, $drLopez, ['impresion_diagnostica' => 'EPOC leve']);
    crearHistoria(Paciente::factory()->create(), $drPerez, [
        'impresion_diagnostica' => 'Artritis reumatoide',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.historial', ['search' => 'EPOC']))
        ->assertOk()
        ->assertSee('EPOC leve')
        ->assertDontSee('Artritis reumatoide');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.historial', ['search' => '1234567890']))
        ->assertOk()
        ->assertSee('EPOC leve')
        ->assertDontSee('Artritis reumatoide');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.historial', ['search' => 'Pérez']))
        ->assertOk()
        ->assertSee('Artritis reumatoide')
        ->assertDontSee('EPOC leve');
});

test('el filtro de período usa la fecha de atención', function () {
    $medico = User::factory()->create();
    $paciente = Paciente::factory()->create();

    $hoy = crearHistoria($paciente, $medico, ['impresion_diagnostica' => 'Control de hoy']);
    crearHistoria($paciente, $medico, [
        'fecha_atencion' => now()->subMonths(3)->toDateString(),
        'impresion_diagnostica' => 'Control antiguo',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.historial', ['periodo' => 'hoy']))
        ->assertOk()
        ->assertSee('Control de hoy')
        ->assertDontSee('Control antiguo');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.historial', ['periodo' => 'mes']))
        ->assertOk()
        ->assertSee('Control de hoy')
        ->assertDontSee('Control antiguo');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.historial', ['periodo' => 'anio']))
        ->assertOk()
        ->assertSee('Control antiguo');
});

test('el filtro por médico muestra solo sus atenciones', function () {
    $drLopez = User::factory()->create(['nombre' => 'Carlos', 'apellidos' => 'López']);
    $drPerez = User::factory()->create(['nombre' => 'Juan', 'apellidos' => 'Pérez']);
    $paciente = Paciente::factory()->create();

    crearHistoria($paciente, $drLopez, ['impresion_diagnostica' => 'Control endocrinología']);
    crearHistoria($paciente, $drPerez, ['impresion_diagnostica' => 'Control cardiología']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.historial', ['medico_id' => $drLopez->id]))
        ->assertOk()
        ->assertSee('Control endocrinología')
        ->assertDontSee('Control cardiología')
        ->assertSee($drLopez->nombre_completo); // el select conserva el nombre
});

test('el detalle de una atención incluye signos, prescripciones y próxima cita', function () {
    $medico = User::factory()->create(['nombre' => 'Ana', 'apellidos' => 'Gómez']);
    $paciente = Paciente::factory()->create();
    $historia = crearHistoria($paciente, $medico);

    SignoVital::create([
        'historia_clinica_id' => $historia->id,
        'paciente_id' => $paciente->id,
        'presion_sistolica' => 130,
        'presion_diastolica' => 85,
        'glucosa' => 95,
    ]);

    $medicamento = Medicamento::create([
        'nombre' => 'Metformina',
        'principio_activo' => 'Metformina',
        'forma_farmaceutica' => 'Tableta',
        'es_activo' => true,
    ]);

    Prescripcion::create([
        'historia_clinica_id' => $historia->id,
        'paciente_id' => $paciente->id,
        'medicamento_id' => $medicamento->id,
        'dosis' => '850 mg',
        'frecuencia' => 'cada 12 horas',
        'via_administracion' => 'Oral',
        'duracion_dias' => 90,
        'fecha_inicio' => now()->toDateString(),
        'es_activo' => true,
    ]);

    Cita::create([
        'paciente_id' => $paciente->id,
        'medico_id' => $medico->id,
        'fecha_cita' => now()->addMonth()->toDateString(),
        'hora_inicio' => '08:00',
        'hora_fin' => '08:30',
        'tipo_cita' => 'Control',
        'estado' => 'Programada',
        'motivo' => 'Control mensual',
    ]);

    $this->actingAs(User::factory()->create())
        ->getJson(route('dashboard.historial.show', $historia))
        ->assertOk()
        ->assertJsonPath('impresion_diagnostica', 'Diabetes mellitus tipo 2 controlada')
        ->assertJsonPath('medico.apellidos', 'Gómez')
        ->assertJsonPath('paciente.id', $paciente->id)
        ->assertJsonPath('signos_vitales.0.glucosa', '95.00')
        ->assertJsonPath('prescripciones.0.medicamento.nombre', 'Metformina')
        ->assertJsonPath('prescripciones.0.dosis', '850 mg')
        ->assertJsonPath('proxima_cita.estado', 'Programada');
});

test('el detalle responde 404 para una atención inexistente', function () {
    $this->actingAs(User::factory()->create())
        ->getJson(route('dashboard.historial.show', 999))
        ->assertNotFound();
});

test('la exportación CSV incluye los registros filtrados', function () {
    $medico = User::factory()->create(['nombre' => 'Carlos', 'apellidos' => 'López']);
    $paciente = Paciente::factory()->create(['nombres' => 'María', 'apellidos' => 'García']);

    crearHistoria($paciente, $medico, ['impresion_diagnostica' => 'Diabetes controlada']);
    crearHistoria(Paciente::factory()->create(['nombres' => 'Pedro', 'apellidos' => 'Ruiz']), $medico, [
        'impresion_diagnostica' => 'Asma leve',
    ]);

    $respuesta = $this->actingAs(User::factory()->create())
        ->get(route('dashboard.historial.export', ['search' => 'Diabetes']))
        ->assertOk()
        ->assertDownload('historial-clinico-'.now()->format('Ymd-His').'.csv');

    $contenido = $respuesta->streamedContent();

    expect($contenido)->toContain('Impresión Diagnóstica')
        ->toContain('María García')
        ->toContain('Diabetes controlada')
        ->not->toContain('Asma leve')
        ->not->toContain('Pedro Ruiz');
});

test('el historial muestra estado vacío sin registros', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.historial'))
        ->assertOk()
        ->assertSee('No hay registros en el historial clínico');
});
