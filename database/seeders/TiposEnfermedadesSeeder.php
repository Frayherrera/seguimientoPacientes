<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposEnfermedadesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipos_enfermedades')->insert([
            [
                'nombre' => 'Diabetes Mellitus Tipo 2',
                'descripcion' => 'Enfermedad crónica que afecta la forma en que el cuerpo procesa la glucosa',
                'codigo_cie10' => 'E11',
                'parametros_control' => json_encode([
                    'glucosa' => ['min' => 70, 'max' => 180, 'unidad' => 'mg/dL'],
                    'peso' => ['min' => 40, 'max' => 150, 'unidad' => 'kg'],
                    'presion_sistolica' => ['min' => 90, 'max' => 140, 'unidad' => 'mmHg'],
                ]),
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Hipertensión Arterial',
                'descripcion' => 'Condición crónica caracterizada por presión arterial elevada de forma sostenida',
                'codigo_cie10' => 'I10',
                'parametros_control' => json_encode([
                    'presion_sistolica' => ['min' => 90, 'max' => 140, 'unidad' => 'mmHg'],
                    'presion_diastolica' => ['min' => 60, 'max' => 90, 'unidad' => 'mmHg'],
                    'peso' => ['min' => 40, 'max' => 150, 'unidad' => 'kg'],
                ]),
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Enfermedad Cardiovascular',
                'descripcion' => 'Grupo de trastornos que afectan el corazón y los vasos sanguíneos',
                'codigo_cie10' => 'I25',
                'parametros_control' => json_encode([
                    'presion_sistolica' => ['min' => 90, 'max' => 130, 'unidad' => 'mmHg'],
                    'presion_diastolica' => ['min' => 60, 'max' => 80, 'unidad' => 'mmHg'],
                    'frecuencia_cardiaca' => ['min' => 60, 'max' => 100, 'unidad' => 'lpm'],
                ]),
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Enfermedad Renal Crónica',
                'descripcion' => 'Pérdida progresiva e irreversible de la función renal',
                'codigo_cie10' => 'N18',
                'parametros_control' => json_encode([
                    'presion_sistolica' => ['min' => 90, 'max' => 130, 'unidad' => 'mmHg'],
                    'peso' => ['min' => 35, 'max' => 120, 'unidad' => 'kg'],
                ]),
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'EPOC (Enfermedad Pulmonar Obstructiva Crónica)',
                'descripcion' => 'Enfermedad pulmonar progresiva que dificulta la respiración',
                'codigo_cie10' => 'J44',
                'parametros_control' => json_encode([
                    'saturacion_oxigeno' => ['min' => 88, 'max' => 100, 'unidad' => '%'],
                    'frecuencia_respiratoria' => ['min' => 12, 'max' => 20, 'unidad' => 'rpm'],
                ]),
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
