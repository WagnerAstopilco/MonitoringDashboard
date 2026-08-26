<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.change_status',
            'users.reset_password',

            // Clients
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',
            'clients.search',

            // Services
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'services.change_status',
            'services.sync_promotions',

            // Promotions
            'promotions.view',
            'promotions.create',
            'promotions.edit',
            'promotions.delete',
            'promotions.change_status',
            'promotions.sync_services',

            // Payment Methods
            'payment_methods.view',
            'payment_methods.create',
            'payment_methods.edit',
            'payment_methods.delete',

            // Transactions
            'transactions.view',
            'transactions.create',
            'transactions.edit',
            'transactions.delete',
            'transactions.change_delivery_status',
            'transactions.reports',

            // Payments
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',

            //main
            'dashboard.view',
            'salesboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $employee = Role::firstOrCreate([
            'name' => 'employee',
            'guard_name' => 'web',
        ]);

        $visit = Role::firstOrCreate([
            'name' => 'visit',
            'guard_name' => 'web',
        ]);

        // Permisos del administrador
        $admin->syncPermissions(Permission::all());

        // Permisos del empleado
        $employee->syncPermissions([

            'salesboard.view',

            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.search',

            'services.view',

            'promotions.view',

            'transactions.view',
            'transactions.create',
            'transactions.change_delivery_status',

            'payments.view',
            'payments.create',

            'payment_methods.view',
        ]);

        // Permisos del visitante
        $visit->syncPermissions([
            'dashboard.view',
            'salesboard.view',

            'transactions.reports',

            'transactions.view',

            'payment_methods.view',
        ]);
    }
}
