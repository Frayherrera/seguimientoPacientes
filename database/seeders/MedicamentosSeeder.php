<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicamentosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('medicamentos')->insert([
            // Medicamentos para Diabetes
            [
                'nombre' => 'Metformina',
                'principio_activo' => 'Metformina hidrocloruro',
                'forma_farmaceutica' => 'Tableta',
                'concentracion' => '850mg',
                'laboratorio' => 'Genérico',
                'es_controlado' => false,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Glimepirida',
                'principio_activo' => 'Glimepirida',
                'forma_farmaceutica' => 'Tableta',
                'concentracion' => '2mg',
                'laboratorio' => 'Genérico',
                'es_controlado' => false,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Insulina Glargina',
                'principio_activo' => 'Insulina glargina',
                'forma_farmaceutica' => 'Solución inyectable',
                'concentracion' => '100 UI/mL',
                'laboratorio' => 'Lantus',
                'es_controlado' => true,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Medicamentos para Hipertensión
            [
                'nombre' => 'Losartán',
                'principio_activo' => 'Losartán potásico',
                'forma_farmaceutica' => 'Tableta',
                'concentracion' => '50mg',
                'laboratorio' => 'Genérico',
                'es_controlado' => false,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Enalapril',
                'principio_activo' => 'Enalapril maleato',
                'forma_farmaceutica' => 'Tableta',
                'concentracion' => '20mg',
                'laboratorio' => 'Genérico',
                'es_controlado' => false,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Amlodipino',
                'principio_activo' => 'Besilato de amlodipino',
                'forma_farmaceutica' => 'Tableta',
                'concentracion' => '5mg',
                'laboratorio' => 'Genérico',
                'es_controlado' => false,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Hidroclorotiazida',
                'principio_activo' => 'Hidroclorotiazida',
                'forma_farmaceutica' => 'Tableta',
                'concentracion' => '25mg',
                'laboratorio' => 'Genérico',
                'es_controlado' => false,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Estatinas
            [
                'nombre' => 'Atorvastatina',
                'principio_activo' => 'Atorvastatina cálcica',
                'forma_farmaceutica' => 'Tableta',
                'concentracion' => '20mg',
                'laboratorio' => 'Genérico',
                'es_controlado' => false,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Antiagregantes
            [
                'nombre' => 'Ácido acetilsalicílico',
                'principio_activo' => 'Ácido acetilsalicílico',
                'forma_farmaceutica' => 'Tableta',
                'concentracion' => '100mg',
                'laboratorio' => 'Genérico',
                'es_controlado' => false,
                'es_activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
