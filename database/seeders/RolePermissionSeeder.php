<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Modules covered by the ERP. Each gets a standard CRUD-style permission set,
     * generated programmatically to avoid hundreds of repetitive lines.
     */
    protected array $modules = [
        'dashboard' => ['view'],
        'customers' => ['view', 'create', 'edit', 'delete', 'export'],
        'milk-rates' => ['view', 'create', 'edit', 'delete'],
        'daily-entries' => ['view', 'create', 'edit', 'delete', 'lock-override'],
        'calendar' => ['view', 'create', 'edit', 'delete'],
        'holidays' => ['view', 'create', 'edit', 'delete'],
        'birthdays' => ['view', 'create', 'edit', 'delete'],
        'bills' => ['view', 'create', 'edit', 'delete', 'generate', 'export', 'print'],
        'payments' => ['view', 'create', 'edit', 'delete', 'export', 'print'],
        'expenses' => ['view', 'create', 'edit', 'delete', 'export'],
        'employees' => ['view', 'create', 'edit', 'delete'],
        'routes' => ['view', 'create', 'edit', 'delete'],
        'products' => ['view', 'create', 'edit', 'delete'],
        'inventory' => ['view', 'create', 'edit', 'delete'],
        'suppliers' => ['view', 'create', 'edit', 'delete'],
        'milk-purchases' => ['view', 'create', 'edit', 'delete'],
        'milk-stock' => ['view', 'create'],
        'cash-sales' => ['view', 'create', 'edit', 'delete'],
        'reports' => ['view', 'export'],
        'settings' => ['view', 'edit'],
        'users' => ['view', 'create', 'edit', 'delete'],
        'roles' => ['view', 'create', 'edit', 'delete'],
        'activity-logs' => ['view', 'export', 'manage'],
    ];

    /**
     * Role => module permissions they get. 'all' expands to every permission generated above.
     */
    protected array $roleMatrix = [
        'Admin' => 'all',
        'Manager' => [
            'dashboard' => ['view'],
            'customers' => ['view', 'create', 'edit', 'export'],
            'milk-rates' => ['view', 'create', 'edit'],
            'daily-entries' => ['view', 'create', 'edit', 'lock-override'],
            'calendar' => ['view', 'create', 'edit', 'delete'],
            'holidays' => ['view', 'create', 'edit'],
            'birthdays' => ['view', 'create', 'edit'],
            'bills' => ['view', 'create', 'edit', 'generate', 'export', 'print'],
            'payments' => ['view', 'create', 'edit', 'export', 'print'],
            'expenses' => ['view', 'create', 'edit', 'export'],
            'employees' => ['view', 'create', 'edit'],
            'routes' => ['view', 'create', 'edit'],
            'products' => ['view', 'create', 'edit'],
            'inventory' => ['view', 'create', 'edit'],
            'suppliers' => ['view', 'create', 'edit'],
            'milk-purchases' => ['view', 'create', 'edit'],
            'milk-stock' => ['view', 'create'],
            'cash-sales' => ['view', 'create', 'edit'],
            'reports' => ['view', 'export'],
            'activity-logs' => ['view', 'export'],
        ],
        'Accountant' => [
            'dashboard' => ['view'],
            'customers' => ['view', 'export'],
            'calendar' => ['view', 'create'],
            'holidays' => ['view'],
            'birthdays' => ['view'],
            'bills' => ['view', 'create', 'edit', 'generate', 'export', 'print'],
            'payments' => ['view', 'create', 'edit', 'export', 'print'],
            'expenses' => ['view', 'create', 'edit', 'export'],
            'milk-purchases' => ['view'],
            'milk-stock' => ['view'],
            'cash-sales' => ['view'],
            'reports' => ['view', 'export'],
            'activity-logs' => ['view', 'export'],
        ],
        'Operator' => [
            'dashboard' => ['view'],
            'customers' => ['view', 'create', 'edit'],
            'daily-entries' => ['view', 'create', 'edit'],
            'calendar' => ['view', 'create', 'edit'],
            'holidays' => ['view'],
            'birthdays' => ['view'],
            'cash-sales' => ['view', 'create'],
            'payments' => ['view', 'create'],
            'activity-logs' => ['view', 'export'],
        ],
        'Delivery Boy' => [
            'dashboard' => ['view'],
            'customers' => ['view'],
            'daily-entries' => ['view', 'create', 'edit'],
            'calendar' => ['view'],
            'holidays' => ['view'],
            'birthdays' => ['view'],
            'payments' => ['view', 'create'],
            'activity-logs' => ['view', 'export'],
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function () {
            $allPermissionNames = [];

            foreach ($this->modules as $module => $actions) {
                foreach ($actions as $action) {
                    $name = "{$module}.{$action}";
                    Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
                    $allPermissionNames[] = $name;
                }
            }

            foreach ($this->roleMatrix as $roleName => $permissions) {
                $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

                if ($permissions === 'all') {
                    $role->syncPermissions($allPermissionNames);
                    continue;
                }

                $names = [];
                foreach ($permissions as $module => $actions) {
                    foreach ($actions as $action) {
                        $names[] = "{$module}.{$action}";
                    }
                }
                $role->syncPermissions($names);
            }
        });
    }
}
