<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir los roles
        $roles = ['ADMIN', 'FREE', 'PREMIUM'];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // Crear el usuario administrador
        $admin = User::factory()->create([
            'name' => 'Administrador',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('1234'),
            'rol' => 'ADMIN',
        ]);
        $admin->assignRole('ADMIN');

        /**
         * // Crear usuarios individuales
         * $user1 = User::factory()->create([
         * 'name' => 'Premium',
         * 'username' => 'premium',
         * 'email' => 'premium@gmail.com',
         * 'password' => bcrypt('1234'),
         * 'rol' => 'PREMIUM',
         * ]);
         * $user1->assignRole('PREMIUM');
         *
         * $user2 = User::factory()->create([
         * 'name' => 'Free',
         * 'username' => 'free',
         * 'email' => 'free@gmail.com',
         * 'password' => bcrypt('1234'),
         * 'rol' => 'FREE',
         * ]);
         * $user2->assignRole('FREE');
         *
         * $user3 = User::factory()->create([
         * 'name' => 'Alejandro Ruiz',
         * 'username' => 'Ruizinator',
         * 'email' => 'alejandroruiz@gmail.com',
         * 'password' => bcrypt('1234'),
         * 'rol' => 'PREMIUM',
         * 'plan' => 'premium',
         * ]);
         * $user3->assignRole('PREMIUM');
         *
         * $user4 = User::factory()->create([
         * 'name' => 'Lucía Fernández',
         * 'username' => 'LuluFunk',
         * 'email' => 'luciafernandez@gmail.com',
         * 'password' => bcrypt('1234'),
         * 'rol' => 'FREE',
         * 'plan' => 'free',
         * ]);
         * $user4->assignRole('FREE');
         *
         * $user5 = User::factory()->create([
         * 'name' => 'David Ramírez',
         * 'username' => 'Ramichamp',
         * 'email' => 'davidramirez@gmail.com',
         * 'password' => bcrypt('1234'),
         * 'rol' => 'PREMIUM',
         * 'plan' => 'premium',
         * ]);
         * $user5->assignRole('PREMIUM');
         *
         * $user6 = User::factory()->create([
         * 'name' => 'Sofía López',
         * 'username' => 'SofyQueen',
         * 'email' => 'sofialopez@gmail.com',
         * 'password' => bcrypt('1234'),
         * 'rol' => 'FREE',
         * 'plan' => 'free',
         * ]);
         * $user6->assignRole('FREE');
         *
         * $user7 = User::factory()->create([
         * 'name' => 'Pablo Sánchez',
         * 'username' => 'SanchyPro',
         * 'email' => 'pablosanchez@gmail.com',
         * 'password' => bcrypt('1234'),
         * 'rol' => 'PREMIUM',
         * 'plan' => 'premium',
         * ]);
         * $user7->assignRole('PREMIUM');
         *
         * $user8 = User::factory()->create([
         * 'name' => 'Laura Martínez',
         * 'username' => 'LaurisCool',
         * 'email' => 'lauramartinez@gmail.com',
         * 'password' => bcrypt('1234'),
         * 'rol' => 'FREE',
         * 'plan' => 'free',
         * ]);
         * $user8->assignRole('FREE');
         *
         * $user9 = User::factory()->create([
         * 'name' => 'Javier Torres',
         * 'username' => 'JaviMaster',
         * 'email' => 'javiertorres@gmail.com',
         * 'password' => bcrypt('1234'),
         * 'rol' => 'PREMIUM',
         * 'plan' => 'premium',
         * ]);
         * $user9->assignRole('PREMIUM');
         *
         * $user10 = User::factory()->create([
         * 'name' => 'Elena Díaz',
         * 'username' => 'ElenitaXD',
         * 'email' => 'elenadiaz@gmail.com',
         * 'password' => bcrypt('1234'),
         * 'rol' => 'FREE',
         * 'plan' => 'free',
         * ]);
         * $user10->assignRole('FREE');
         *
         * $user11 = User::factory()->create([
         * 'name' => 'Javier Núñez',
         * 'username' => 'javinunnezz',
         * 'email' => 'javint321@gmail.com',
         * 'password' => bcrypt('1234'),
         * 'rol' => 'PREMIUM',
         * 'plan' => 'premium',
         * ]);
         * $user11->assignRole('PREMIUM');
         */
    }
}
