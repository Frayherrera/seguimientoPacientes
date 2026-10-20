<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // El orden es importante por las foreign keys
        $this->call([
            RolesSeeder::class,
            TiposEnfermedadesSeeder::class,
            UsersSeeder::class,
            MedicamentosSeeder::class,
            ConfiguracionAlertasSeeder::class,
        ]);
    }
}
