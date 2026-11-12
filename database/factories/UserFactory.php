<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Por defecto Administrador (acceso total); usar ->rol('Médico') para otros
            'role_id' => fn () => Role::firstOrCreate(['nombre' => 'Administrador'])->id,
            'nombre' => fake()->firstName(),
            'apellidos' => fake()->lastName().' '.fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'telefono' => fake()->numerify('3#########'),
            'especialidad' => null,
            'registro_medico' => null,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'es_activo' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Usuario con el rol indicado (por nombre, como en RolesSeeder).
     */
    public function rol(string $nombre): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstOrCreate(['nombre' => $nombre])->id,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
