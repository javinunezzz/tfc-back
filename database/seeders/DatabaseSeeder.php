<?php

namespace Database\Seeders;

use App\Models\Apunte;
use App\Models\Descarga;
use App\Models\Suscripcion;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(ConfiguracionSeeder::class);
        $this->call(CategoriaSeeder::class);
        $this->call(AsignaturaSeeder::class);
        // Lo comento para hacer una prueba real
        // Apunte::factory()->count(500)->create();
    }
}
