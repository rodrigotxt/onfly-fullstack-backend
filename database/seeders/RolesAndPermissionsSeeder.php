<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder

{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir o guard padrão para API durante a criação
        config(['permission.default_guard' => 'api']);

        // Criar permissões
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view travel orders',
            'create travel orders',
            'update travel orders',
            'delete travel orders',
            'update travel order status',
            'manage roles',
            'manage permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        // Criar roles e atribuir permissões
        $adminRole = Role::findOrCreate('admin', 'api');
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::findOrCreate('manager', 'api');
        $managerRole->givePermissionTo([
            'view users', 'create users', 'edit users',
            'view travel orders', 'create travel orders', 
            'update travel orders', 'update travel order status'
        ]);

        $basicUserRole = Role::findOrCreate('basic_user', 'api');
        $basicUserRole->givePermissionTo([
            'view travel orders', 'create travel orders'
        ]);

        // Criar usuários de exemplo
        $this->createUserWithRole(
            'Admin User',
            'admin@example.com',
            'password',
            'admin'
        );

        $this->createUserWithRole(
            'Manager User',
            'manager@example.com',
            'password',
            'manager'
        );

        $this->createUserWithRole(
            'Basic User',
            'user@example.com',
            'password',
            'basic_user'
        );
    }

    protected function createUserWithRole($name, $email, $password, $role)
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt($password)
            ]
        );

        if (!$user->hasRole($role)) {
            $user->guard_name = 'api';
            $user->assignRole($role);
            $this->command->info("User {$email} assigned to role: {$role} (api guard)");
        }
    }
}