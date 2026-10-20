<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema, gestión de usuarios y configuraciones',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Médico',
                'descripcion' => 'Registro de historias clínicas, prescripciones y seguimiento de pacientes',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Enfermero',
                'descripcion' => 'Registro de signos vitales, aplicación de medicamentos y apoyo en consultas',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Administrativo',
                'descripcion' => 'Gestión de citas, facturación y reportes administrativos',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
