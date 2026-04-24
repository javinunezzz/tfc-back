<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Configuracion::create(['parametro' => 'dominios-permitidos', 'valor' => 'gmail.com,iescastelar.com,educarex.es']);
    }
}
