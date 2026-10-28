<?php

namespace Database\Factories;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Paciente>
 */
class PacienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'documento_identidad' => fake()->unique()->numerify('##########'),
            'tipo_documento' => fake()->randomElement(['CC', 'CC', 'CC', 'CE', 'TI', 'PAS']),
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName().' '.fake()->lastName(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-80 years', '-10 years')->format('Y-m-d'),
            'genero' => fake()->randomElement(['M', 'F', 'Otro']),
            'telefono' => fake()->numerify('2#######'),
            'celular' => fake()->numerify('3#########'),
            'email' => fake()->unique()->safeEmail(),
            'direccion' => fake()->streetAddress(),
            'ciudad' => fake()->city(),
            'departamento' => fake()->state(),
            'eps' => fake()->randomElement(['Sura', 'Sanitas', 'Compensar', 'Nueva EPS', 'Salud Total']),
            'ips_primaria' => null,
            'estado' => 'Activo',
            'observaciones' => null,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => ['estado' => 'Inactivo']);
    }
}
