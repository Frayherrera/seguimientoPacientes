<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    public function definition(): array
    {
        // Nombres sintácticos a propósito: no deben colisionar con los roles
        // sembrados ni con los Role::create() de los tests (nombre es unique)
        return [
            'nombre' => fake()->unique()->lexify('Rol_??????'),
            'descripcion' => fake()->sentence(4),
            'es_activo' => true,
        ];
    }
}
