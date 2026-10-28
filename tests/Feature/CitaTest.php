<?php

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function medicoPruebas(array $overrides = []): User
{
    $rolMedico = Role::firstOrCreate(['nombre' => 'Médico']);

    return User::factory()->create(array_merge(['role_id' => $rolMedico->id], $overrides));
}

function datosCita(array $overrides = []): array
{
    return array_merge([
        'paciente_id' => 0, // se rellena en cada test
        'medico_id' => 0,
        'fecha_cita' => now()->addDay()->toDateString(),
        'hora_inicio' => '09:00',
        'tipo_cita' => 'Control',
        'motivo' => 'Control de glucosa',
    ], $overrides);
}

function crearCita(Paciente $paciente, User $medico, array $overrides = []): Cita
{
    return Cita::create(datosCita(array_merge([
        'paciente_id' => $paciente->id,
        'medico_id' => $medico->id,
    ], $overrides)));
}

test('las citas requieren autenticación', function () {
    $this->get(route('dashboard.citas'))->assertRedirect(route('login'));
});

test('el índice muestra las próximas citas y no las pasadas por defecto', function () {
    $medico = medicoPruebas();
    $paciente = Paciente::factory()->create();

    crearCita($paciente, $medico, ['motivo' => 'Control pendiente futuro']);
    crearCita($paciente, $medico, [
        'fecha_cita' => now()->subDays(5)->toDateString(),
        'motivo' => 'Cita pasada ya atendida',
        'estado' => 'Atendida',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.citas'))
        ->assertOk()
        ->assertSee('Control pendiente futuro')
        ->assertDontSee('Cita pasada ya atendida')
        ->assertSee($paciente->nombre_completo);
});

test('el índice calcula estadísticas y agenda de hoy con datos reales', function () {
    $medico = medicoPruebas();
    $paciente = Paciente::factory()->create();

    crearCita($paciente, $medico, ['fecha_cita' => now()->toDateString(), 'motivo' => 'Cita de hoy pendiente']);
    crearCita($paciente, $medico, [
        'fecha_cita' => now()->toDateString(),
        'hora_inicio' => '10:00',
        'motivo' => 'Cita de hoy atendida',
        'estado' => 'Atendida',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.citas'))
        ->assertOk()
        ->assertSee('Citas Hoy')
        ->assertSeeInOrder(['Agenda de Hoy', '1', 'Atendidas', '1', 'Pendientes']);
});

test('el filtro de estado y el período funcionan en el servidor', function () {
    $medico = medicoPruebas();
    $paciente = Paciente::factory()->create();

    crearCita($paciente, $medico, ['motivo' => 'Consulta confirmada', 'estado' => 'Confirmada']);
    crearCita($paciente, $medico, [
        'fecha_cita' => now()->subDays(2)->toDateString(),
        'hora_inicio' => '08:00',
        'motivo' => 'Consulta cancelada pasada',
        'estado' => 'Cancelada',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.citas', ['estado' => 'Confirmada']))
        ->assertOk()
        ->assertSee('Consulta confirmada')
        ->assertDontSee('Consulta cancelada pasada');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.citas', ['periodo' => 'pasadas']))
        ->assertOk()
        ->assertSee('Consulta cancelada pasada')
        ->assertDontSee('Consulta confirmada');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.citas', ['periodo' => 'todas', 'search' => 'cancelada']))
        ->assertOk()
        ->assertSee('Consulta cancelada pasada')
        ->assertDontSee('Consulta confirmada');
});

test('la búsqueda cubre paciente, médico y motivo', function () {
    $medico = medicoPruebas(['nombre' => 'Carlos', 'apellidos' => 'Martínez']);
    $paciente = Paciente::factory()->create(['documento_identidad' => '555444333']);

    crearCita($paciente, $medico, ['motivo' => 'Control de presión arterial']);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.citas', ['search' => 'Martínez']))
        ->assertOk()
        ->assertSee('Control de presión arterial');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.citas', ['search' => '555444333']))
        ->assertOk()
        ->assertSee('Control de presión arterial');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.citas', ['search' => 'presión']))
        ->assertOk()
        ->assertSee('Control de presión arterial');
});

test('se puede programar una cita y nace como programada', function () {
    $medico = medicoPruebas();
    $paciente = Paciente::factory()->create();

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.citas.store'), datosCita([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
        ]))
        ->assertRedirect(route('dashboard.citas'))
        ->assertSessionHas('success');

    $cita = Cita::first();

    expect($cita)->not->toBeNull()
        ->and($cita->estado)->toBe('Programada')
        ->and($cita->motivo)->toBe('Control de glucosa')
        ->and($cita->recordatorio_enviado)->toBeFalse();
});

test('el médico no puede tener dos citas en la misma fecha y hora', function () {
    $medico = medicoPruebas();
    $otroMedico = medicoPruebas();
    $paciente = Paciente::factory()->create();

    crearCita($paciente, $medico);

    // El mismo médico, mismo slot → conflicto
    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.citas.store'), datosCita([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
        ]))
        ->assertSessionHasErrors('hora_inicio');

    // Otro médico al mismo slot → permitido
    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.citas.store'), datosCita([
            'paciente_id' => $paciente->id,
            'medico_id' => $otroMedico->id,
        ]))
        ->assertSessionHasNoErrors();

    // Una cita cancelada no bloquea el slot
    crearCita($paciente, $medico, [
        'hora_inicio' => '11:00',
        'estado' => 'Cancelada',
    ]);

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.citas.store'), datosCita([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'hora_inicio' => '11:00',
        ]))
        ->assertSessionHasNoErrors();

    expect(Cita::count())->toBe(4);
});

test('la creación valida fecha futura, horas y campos requeridos', function () {
    $medico = medicoPruebas();
    $paciente = Paciente::factory()->create();

    $this->actingAs(User::factory()->create())
        ->from(route('dashboard.citas'))
        ->post(route('dashboard.citas.store'), ['motivo' => ''])
        ->assertSessionHasErrors(['paciente_id', 'medico_id', 'fecha_cita', 'hora_inicio', 'tipo_cita', 'motivo']);

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.citas.store'), datosCita([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha_cita' => now()->subDay()->toDateString(),
        ]))
        ->assertSessionHasErrors('fecha_cita');

    $this->actingAs(User::factory()->create())
        ->post(route('dashboard.citas.store'), datosCita([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'hora_fin' => '08:00', // antes de hora_inicio (09:00)
        ]))
        ->assertSessionHasErrors('hora_fin');

    expect(Cita::count())->toBe(0);
});

test('una cita se puede editar y reprogramar', function () {
    $medico = medicoPruebas();
    $paciente = Paciente::factory()->create();
    $cita = crearCita($paciente, $medico);

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.citas.update', $cita), datosCita([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha_cita' => now()->addDays(3)->toDateString(),
            'hora_inicio' => '15:00',
            'motivo' => 'Cita reprogramada',
        ]))
        ->assertRedirect(route('dashboard.citas'))
        ->assertSessionHas('success');

    $cita->refresh();

    expect($cita->motivo)->toBe('Cita reprogramada')
        ->and($cita->fecha_cita->toDateString())->toBe(now()->addDays(3)->toDateString())
        ->and(substr((string) $cita->hora_inicio, 0, 5))->toBe('15:00');
});

test('una cita atendida o cancelada no se puede editar', function () {
    $medico = medicoPruebas();
    $paciente = Paciente::factory()->create();
    $cita = crearCita($paciente, $medico, ['estado' => 'Atendida']);

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.citas.update', $cita), datosCita([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'motivo' => 'Intento de edición',
        ]))
        ->assertSessionHas('error');

    expect($cita->fresh()->motivo)->toBe('Control de glucosa');
});

test('la transición de estados sigue el flujo programada→confirmada→atendida', function () {
    $medico = medicoPruebas();
    $paciente = Paciente::factory()->create();
    $cita = crearCita($paciente, $medico);

    // Programada → Confirmada: permitido
    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.citas.estado', $cita), ['estado' => 'Confirmada'])
        ->assertSessionHas('success');
    expect($cita->fresh()->estado)->toBe('Confirmada');

    // Confirmada → Atendida: permitido
    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.citas.estado', $cita), ['estado' => 'Atendida'])
        ->assertSessionHas('success');
    expect($cita->fresh()->estado)->toBe('Atendida');

    // Atendida → Cancelada: terminal, rechazado
    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.citas.estado', $cita), ['estado' => 'Cancelada'])
        ->assertSessionHas('error');
    expect($cita->fresh()->estado)->toBe('Atendida');
});

test('una cita programada no puede pasar directamente a atendida', function () {
    $medico = medicoPruebas();
    $paciente = Paciente::factory()->create();
    $cita = crearCita($paciente, $medico);

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.citas.estado', $cita), ['estado' => 'Atendida'])
        ->assertSessionHas('error');

    expect($cita->fresh()->estado)->toBe('Programada');
});

test('el cambio de estado valida el valor recibido', function () {
    $medico = medicoPruebas();
    $paciente = Paciente::factory()->create();
    $cita = crearCita($paciente, $medico);

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.citas.estado', $cita), ['estado' => 'Programada'])
        ->assertSessionHasErrors('estado');

    $this->actingAs(User::factory()->create())
        ->put(route('dashboard.citas.estado', $cita), ['estado' => 'Inventada'])
        ->assertSessionHasErrors('estado');

    expect($cita->fresh()->estado)->toBe('Programada');
});
