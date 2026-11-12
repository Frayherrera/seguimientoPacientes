<?php

use App\Models\ConfiguracionAlerta;
use App\Models\Paciente;
use App\Models\Role;
use App\Models\TipoEnfermedad;
use App\Models\User;

// Matriz de permisos: config/permisos.php

dataset('acceso_modulos', [
    // [rol, ruta, ¿puede entrar?]
    ['Administrador', 'dashboard.usuarios', true],
    ['Médico', 'dashboard.usuarios', false],
    ['Enfermero', 'dashboard.usuarios', false],
    ['Administrativo', 'dashboard.usuarios', false],

    ['Médico', 'dashboard.pacientes', true],
    ['Enfermero', 'dashboard.pacientes', true],
    ['Administrativo', 'dashboard.pacientes', true],

    ['Médico', 'dashboard.enfermedades', true],
    ['Enfermero', 'dashboard.enfermedades', true],
    ['Administrativo', 'dashboard.enfermedades', false],

    ['Médico', 'dashboard.seguimiento', true],
    ['Enfermero', 'dashboard.seguimiento', true],
    ['Administrativo', 'dashboard.seguimiento', false],

    ['Médico', 'dashboard.historial', true],
    ['Enfermero', 'dashboard.historial', true],
    ['Administrativo', 'dashboard.historial', false],
    ['Administrativo', 'dashboard.historial.export', false],

    ['Médico', 'dashboard.citas', true],
    ['Enfermero', 'dashboard.citas', true],
    ['Administrativo', 'dashboard.citas', true],

    ['Médico', 'dashboard.reportes', true],
    ['Administrativo', 'dashboard.reportes', true],
    ['Enfermero', 'dashboard.reportes', false],
    ['Enfermero', 'dashboard.reportes.export', false],

    ['Enfermero', 'dashboard.configuracion', true],
    ['Administrativo', 'dashboard.index', true],
]);

test('cada rol solo entra a los módulos permitidos', function (string $rol, string $ruta, bool $puede) {
    $this->actingAs(User::factory()->rol($rol)->create())
        ->get(route($ruta))
        ->assertStatus($puede ? 200 : 403);
})->with('acceso_modulos');

test('el sidebar solo muestra los módulos permitidos para el rol', function () {
    $this->actingAs(User::factory()->rol('Administrativo')->create())
        ->get(route('dashboard.index'))
        ->assertOk()
        ->assertSee(route('dashboard.pacientes'))
        ->assertSee(route('dashboard.citas'))
        ->assertSee(route('dashboard.reportes'))
        ->assertDontSee(route('dashboard.usuarios'))
        ->assertDontSee(route('dashboard.seguimiento'))
        ->assertDontSee(route('dashboard.historial'));
});

test('un enfermero no puede crear pacientes ni registrar controles', function () {
    $enfermero = User::factory()->rol('Enfermero')->create();
    $paciente = Paciente::factory()->create();

    $this->actingAs($enfermero)
        ->post(route('dashboard.pacientes.store'), ['nombres' => 'X'])
        ->assertForbidden();

    $this->actingAs($enfermero)
        ->post(route('dashboard.seguimiento.store'), ['paciente_id' => $paciente->id])
        ->assertForbidden();

    $this->actingAs($enfermero)
        ->get(route('dashboard.pacientes'))
        ->assertOk()
        ->assertDontSee('onclick="openPatientModal()"', false)
        ->assertDontSee('onclick="editPaciente(', false);
});

test('solo el administrador puede eliminar pacientes', function () {
    $paciente = Paciente::factory()->create();

    $this->actingAs(User::factory()->rol('Médico')->create())
        ->delete(route('dashboard.pacientes.destroy', $paciente))
        ->assertForbidden();

    expect(Paciente::find($paciente->id))->not->toBeNull();
});

test('un enfermero ve las citas en solo lectura', function () {
    $enfermero = User::factory()->rol('Enfermero')->create();

    $this->actingAs($enfermero)
        ->get(route('dashboard.citas'))
        ->assertOk()
        ->assertDontSee('onclick="openCitaModal()"', false)
        ->assertSee('const puedeGestionarCitas = false', false);

    $this->actingAs($enfermero)
        ->post(route('dashboard.citas.store'), [])
        ->assertForbidden();
});

test('un administrativo no puede gestionar el catálogo de enfermedades', function () {
    $enfermedad = TipoEnfermedad::create(['nombre' => 'Diabetes', 'es_activo' => true]);

    $this->actingAs(User::factory()->rol('Administrativo')->create())
        ->delete(route('dashboard.enfermedades.destroy', $enfermedad))
        ->assertForbidden();
});

test('solo el administrador ve y gestiona los umbrales de alerta', function () {
    $enfermedad = TipoEnfermedad::create(['nombre' => 'Hipertensión', 'es_activo' => true]);
    $umbral = ConfiguracionAlerta::create([
        'tipo_enfermedad_id' => $enfermedad->id,
        'nombre_parametro' => 'presion_sistolica',
        'valor_minimo' => 90,
        'valor_maximo' => 140,
        'unidad' => 'mmHg',
        'nivel_alerta' => 'Alto',
        'es_activo' => true,
    ]);
    $medico = User::factory()->rol('Médico')->create();

    $this->actingAs($medico)
        ->get(route('dashboard.configuracion'))
        ->assertOk()
        ->assertDontSee('Umbrales de Alerta Clínicas');

    $this->actingAs($medico)
        ->delete(route('dashboard.configuracion.umbrales.destroy', $umbral))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.configuracion'))
        ->assertSee('Umbrales de Alerta Clínicas');
});

test('un usuario desactivado no puede iniciar sesión', function () {
    $usuario = User::factory()->create(['es_activo' => false]);

    $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('un usuario con rol desactivado no puede iniciar sesión', function () {
    $rol = Role::create(['nombre' => 'Enfermero', 'es_activo' => false]);
    $usuario = User::factory()->create(['role_id' => $rol->id]);

    $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('si desactivan al usuario con la sesión abierta, se le cierra la sesión', function () {
    $usuario = User::factory()->rol('Médico')->create();
    $this->actingAs($usuario)->get(route('dashboard.index'))->assertOk();

    $usuario->update(['es_activo' => false]);

    $this->actingAs($usuario->fresh())
        ->get(route('dashboard.pacientes'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('el administrador no puede cambiarse su propio rol', function () {
    $admin = User::factory()->create();
    $rolMedico = Role::firstOrCreate(['nombre' => 'Médico']);

    $this->actingAs($admin)
        ->put(route('dashboard.usuarios.update', $admin), [
            'nombre' => $admin->nombre,
            'apellidos' => $admin->apellidos,
            'email' => $admin->email,
            'role_id' => $rolMedico->id,
            'es_activo' => '1',
        ])
        ->assertRedirect(route('dashboard.usuarios'))
        ->assertSessionHas('error');

    expect($admin->fresh()->esAdministrador())->toBeTrue();
});
