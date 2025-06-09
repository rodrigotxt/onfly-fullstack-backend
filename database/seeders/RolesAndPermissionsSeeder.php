<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetar permissões cacheadas
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Criar Permissões
        // Permissões de usuários
        Permission::findOrCreate('view users');
        Permission::findOrCreate('create users');
        Permission::findOrCreate('edit users');
        Permission::findOrCreate('delete users');

        // Permissões de pedidos de viagem
        Permission::findOrCreate('view travel orders');
        Permission::findOrCreate('create travel orders');
        Permission::findOrCreate('update travel orders');
        Permission::findOrCreate('delete travel orders');
        Permission::findOrCreate('update travel order status');

        // Permissões de gerenciamento de roles/permissões
        Permission::findOrCreate('manage roles');
        Permission::findOrCreate('manage permissions');

        // 2. Criar Roles e Atribuir Permissões

        // Role: Admin (Administrador Completo)
        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo(Permission::all()); // Admin tem todas as permissões

        // Role: Manager (Gerente de Pedidos e Usuários)
        $managerRole = Role::findOrCreate('manager');
        $managerRole->givePermissionTo([
            'view users', 'create users', 'edit users',
            'view travel orders', 'create travel orders', 'update travel orders', 'update travel order status'
        ]);

        // Role: BasicUser (Usuário Básico - pode ver e criar seus próprios pedidos)
        $basicUserRole = Role::findOrCreate('basic_user');
        $basicUserRole->givePermissionTo([
            'view travel orders', 'create travel orders'
        ]);


        // Encontre um usuário existente ou crie um
        $user1 = User::find(1);
        if ($user1) {
            $user1->assignRole('admin');
            $this->command->info('User 1 assigned to role: admin');
        } else {
            $adminUser = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('secret123'),
            ]);
            $adminUser->assignRole('admin');
            $this->command->info('New admin user created: admin@example.com');
        }

        $user2 = User::find(2);
        if ($user2) {
            $user2->assignRole('manager');
            $this->command->info('User 2 assigned to role: manager');
        } else {
            $managerUser = User::factory()->create([
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'password' => bcrypt('secret123'),
            ]);
            $managerUser->assignRole('manager');
            $this->command->info('New manager user created: manager@example.com');
        }

        $user3 = User::find(3);
        if ($user3) {
            $user3->assignRole('basic_user');
            $this->command->info('User 3 assigned to role: basic_user');
        } else {
            $basicUser = User::factory()->create([
                'name' => 'Basic User',
                'email' => 'user@example.com',
                'password' => bcrypt('secret123'),
            ]);
            $basicUser->assignRole('basic_user');
            $this->command->info('New basic user created: user@example.com');
        }
    }
}