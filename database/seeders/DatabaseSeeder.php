<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Cuentas de desarrollo. Cambia estas contraseñas antes de publicar la app.
        User::updateOrCreate(
            ['email' => 'admin@almacen.test'],
            ['name' => 'Administrador', 'password' => 'password', 'role' => User::ROLE_ADMIN]
        );

        User::updateOrCreate(
            ['email' => 'empleado@almacen.test'],
            ['name' => 'Empleado', 'password' => 'password', 'role' => User::ROLE_EMPLOYEE]
        );
    }
}