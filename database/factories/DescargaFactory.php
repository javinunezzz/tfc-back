<?php

namespace Database\Factories;

use App\Models\Apunte;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Descarga>
 */
class DescargaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $usuario = User::inRandomOrder()->first();
        $apunte = Apunte::inRandomOrder()->first();

        return [
            'user_id' => $usuario->id,
            'apunte_id' => $apunte->id,
            'fecha_descarga' => now(),
        ];
    }
}
