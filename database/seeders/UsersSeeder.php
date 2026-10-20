<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = DB::table('roles')->where('nombre', 'Administrador')->first();
        $medicoRole = DB::table('roles')->where('nombre', 'Médico')->first();
        $enfermeroRole = DB::table('roles')->where('nombre', 'Enfermero')->first();
        $administrativoRole = DB::table('roles')->where('nombre', 'Administrativo')->first();

        $password = Hash::make('password123');

        DB::table('users')->insert([
            [
                'role_id' => $adminRole->id,
                'nombre' => 'Administrador',
                'apellidos' => 'Sistema',
                'email' => 'admin@clinicaaguachica.com',
                'telefono' => '3001234567',
                'especialidad' => null,
                'registro_medico' => null,
                'password' => $password,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $medicoRole->id,
                'nombre' => 'Carlos',
                'apellidos' => 'García Martínez',
                'email' => 'cgarcia@clinicaaguachica.com',
                'telefono' => '3002345678',
                'especialidad' => 'Medicina Interna',
                'registro_medico' => 'MED-12345',
                'password' => $password,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $medicoRole->id,
                'nombre' => 'María',
                'apellidos' => 'López Hernández',
                'email' => 'mlopez@clinicaaguachica.com',
                'telefono' => '3003456789',
                'especialidad' => 'Endocrinología',
                'registro_medico' => 'MED-23456',
                'password' => $password,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $enfermeroRole->id,
                'nombre' => 'Ana',
                'apellidos' => 'Rodríguez Silva',
                'email' => 'arodriguez@clinicaaguachica.com',
                'telefono' => '3004567890',
                'especialidad' => null,
                'registro_medico' => null,
                'password' => $password,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $administrativoRole->id,
                'nombre' => 'Pedro',
                'apellidos' => 'Sánchez Gómez',
                'email' => 'psanchez@clinicaaguachica.com',
                'telefono' => '3005678901',
                'especialidad' => null,
                'registro_medico' => null,
                'password' => $password,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
