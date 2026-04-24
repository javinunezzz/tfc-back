<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Suscripcion>
 */
class SuscripcionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $usuario = User::inRandomOrder()->first();

        return [
            'user_id' => $usuario->id,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addMonth(),
            'estado' => $this->faker->randomElement(['activo', 'cancelado']),
        ];
    }
}
