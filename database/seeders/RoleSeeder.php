<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear los roles principales
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $vendedorRole = Role::firstOrCreate(['name' => 'Vendedor']);
        $operarioRole = Role::firstOrCreate(['name' => 'Operario']);

        // 2. Asignar el rol de Administrador a tu usuario actual
        $user = User::where('email', 'admin@fabrica.com')->first();
        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}