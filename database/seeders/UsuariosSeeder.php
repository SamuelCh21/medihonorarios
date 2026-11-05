<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Prueba',
            'email' => 'admin@test.com',
            'password' => Hash::make('1234'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Gerencia Prueba',
            'email' => 'gerencia@test.com',
            'password' => Hash::make('1234'),
            'role' => 'gerencia',
        ]);

        // este usuario se enlaza con el Dr. Carlos Mena en ClinicaSeeder
        User::create([
            'name' => 'Carlos Mena',
            'email' => 'medico@test.com',
            'password' => Hash::make('1234'),
            'role' => 'medico',
        ]);
    }
}
