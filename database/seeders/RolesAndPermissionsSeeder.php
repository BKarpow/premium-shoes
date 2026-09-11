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
        // Очищаємо кеш дозволів
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Створюємо або знаходимо права (firstOrCreate замість create)
        $permManageProducts = Permission::firstOrCreate(['name' => 'manage products']);
        $permManageOrders   = Permission::firstOrCreate(['name' => 'manage orders']);
        $permManageUsers    = Permission::firstOrCreate(['name' => 'manage users']);

        // 2. Створюємо або знаходимо ролі
        $adminRole   = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        Role::firstOrCreate(['name' => 'customer']);

        // 3. Призначаємо права ролям
        $adminRole->syncPermissions(Permission::all()); // syncPermissions оновлює права без дублювання
        $managerRole->syncPermissions([$permManageProducts, $permManageOrders]);

        // 4. Призначаємо роль 'admin' першому користувачу
        $user = User::first();
        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}
