<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfiguracionAlertasSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener IDs de tipos de enfermedades
        $diabetes = DB::table('tipos_enfermedades')->where('codigo_cie10', 'E11')->first();
        $hipertension = DB::table('tipos_enfermedades')->where('codigo_cie10', 'I10')->first();
        $cardiovascular = DB::table('tipos_enfermedades')->where('codigo_cie10', 'I25')->first();

        $alertas = [];

        // Configuración para Diabetes
        $alertas = array_merge($alertas, [
            [
                'tipo_enfermedad_id' => $diabetes->id,
                'nombre_parametro' => 'glucosa',
                'valor_minimo' => 70,
                'valor_maximo' => 180,
                'unidad' => 'mg/dL',
                'nivel_alerta' => 'Medio',
                'mensaje_alerta' => 'Glucosa fuera de rango normal para paciente diabético',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_enfermedad_id' => $diabetes->id,
                'nombre_parametro' => 'glucosa',
                'valor_minimo' => 54,
                'valor_maximo' => 300,
                'unidad' => 'mg/dL',
                'nivel_alerta' => 'Alto',
                'mensaje_alerta' => 'VALOR CRÍTICO: Glucosa en rango de emergencia',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_enfermedad_id' => $diabetes->id,
                'nombre_parametro' => 'presion_sistolica',
                'valor_minimo' => 90,
                'valor_maximo' => 140,
                'unidad' => 'mmHg',
                'nivel_alerta' => 'Medio',
                'mensaje_alerta' => 'Presión sistólica fuera de rango para diabético',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Configuración para Hipertensión
        $alertas = array_merge($alertas, [
            [
                'tipo_enfermedad_id' => $hipertension->id,
                'nombre_parametro' => 'presion_sistolica',
                'valor_minimo' => 90,
                'valor_maximo' => 140,
                'unidad' => 'mmHg',
                'nivel_alerta' => 'Medio',
                'mensaje_alerta' => 'Presión sistólica elevada',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_enfermedad_id' => $hipertension->id,
                'nombre_parametro' => 'presion_sistolica',
                'valor_minimo' => 90,
                'valor_maximo' => 180,
                'unidad' => 'mmHg',
                'nivel_alerta' => 'Alto',
                'mensaje_alerta' => 'CRISIS HIPERTENSIVA: Presión sistólica peligrosamente alta',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_enfermedad_id' => $hipertension->id,
                'nombre_parametro' => 'presion_diastolica',
                'valor_minimo' => 60,
                'valor_maximo' => 90,
                'unidad' => 'mmHg',
                'nivel_alerta' => 'Medio',
                'mensaje_alerta' => 'Presión diastólica elevada',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_enfermedad_id' => $hipertension->id,
                'nombre_parametro' => 'presion_diastolica',
                'valor_minimo' => 60,
                'valor_maximo' => 120,
                'unidad' => 'mmHg',
                'nivel_alerta' => 'Alto',
                'mensaje_alerta' => 'CRISIS HIPERTENSIVA: Presión diastólica peligrosamente alta',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Configuración para Enfermedad Cardiovascular
        $alertas = array_merge($alertas, [
            [
                'tipo_enfermedad_id' => $cardiovascular->id,
                'nombre_parametro' => 'frecuencia_cardiaca',
                'valor_minimo' => 60,
                'valor_maximo' => 100,
                'unidad' => 'lpm',
                'nivel_alerta' => 'Medio',
                'mensaje_alerta' => 'Frecuencia cardíaca fuera de rango normal',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_enfermedad_id' => $cardiovascular->id,
                'nombre_parametro' => 'presion_sistolica',
                'valor_minimo' => 90,
                'valor_maximo' => 130,
                'unidad' => 'mmHg',
                'nivel_alerta' => 'Alto',
                'mensaje_alerta' => 'Presión sistólica crítica para paciente cardiovascular',
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('configuracion_alertas')->insert($alertas);
    }
}
