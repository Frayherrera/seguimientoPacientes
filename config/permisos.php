<?php

/*
|--------------------------------------------------------------------------
| Matriz de permisos por rol
|--------------------------------------------------------------------------
|
| Única fuente de verdad de qué rol puede hacer qué. Cada clave es un Gate
| (se registran en AppServiceProvider) que se usa en las rutas
| (middleware `can:<permiso>`), en el sidebar y con @can en las vistas.
| Los nombres de rol son los de la tabla `roles` (RolesSeeder).
|
| El Administrador tiene acceso total (Gate::before), no hace falta listarlo.
| Lo que no aparece aquí (dashboard, perfil propio, cerrar sesiones) solo
| requiere estar autenticado con un usuario y rol activos.
|
*/

return [

    'rol_administrador' => 'Administrador',

    'permisos' => [
        // Usuarios: solo el Administrador (crea las cuentas, no hay registro público)
        'usuarios.gestionar' => [],

        'pacientes.ver' => ['Médico', 'Enfermero', 'Administrativo'],
        'pacientes.gestionar' => ['Médico', 'Administrativo'],
        'pacientes.eliminar' => [],

        'enfermedades.ver' => ['Médico', 'Enfermero'],
        'enfermedades.gestionar' => ['Médico'],

        'seguimiento.ver' => ['Médico', 'Enfermero'],
        // Un control crea la historia clínica con medico_id = usuario autenticado
        'seguimiento.registrar' => ['Médico'],
        'alertas.atender' => ['Médico', 'Enfermero'],

        'historial.ver' => ['Médico', 'Enfermero'],

        'citas.ver' => ['Médico', 'Enfermero', 'Administrativo'],
        'citas.gestionar' => ['Médico', 'Administrativo'],

        'reportes.ver' => ['Médico', 'Administrativo'],

        'umbrales.gestionar' => [],
    ],

];
