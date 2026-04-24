<?php

namespace Database\Seeders;

use App\Models\Asignatura;
use Illuminate\Database\Seeder;

class AsignaturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $asignaturas = [
            'Matemáticas',
            'Física',
            'Química',
            'Historia',
            'Lengua y Literatura',
            'Inglés',
            'Filosofía',
            'Biología',
            'Economía',
            'Dibujo Técnico',
            'Educación Física',
            'Informática',
        ];

        foreach ($asignaturas as $nombre) {
            Asignatura::create(['nombre' => $nombre]);
        }
    }
}
