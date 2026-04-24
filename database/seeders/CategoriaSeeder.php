<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //$categorias = [
        //    'Universidades',
        //    'EBAU',
        //    'Bachillerato',
        //    'E.S.O.',
        //    'F.P.',
        //    'Posgrados',
        //    'Oposiciones'
        //];

        $categorias = [
            'EBAU',
            'Bachillerato',
            'E.S.O.',
        ];

        foreach ($categorias as $nombre) {
            Categoria::create(['nombre' => $nombre]);
        }
    }
}
