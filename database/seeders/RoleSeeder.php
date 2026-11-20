<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Categorias
            'create-categories',
            'read-categories',
            'update-categories',
            'delete-categories',

            // Productos
            'create-products',
            'read-products',
            'update-products',
            'delete-products',

            // Almacenes
            'create-warehouses',
            'read-warehouses',
            'update-warehouses',
            'delete-warehouses',

            // Proveedores
            'create-suppliers',
            'read-suppliers',
            'update-suppliers',
            'delete-suppliers',

            // Ordenes de compra
            'create-purchase-orders',
            'read-purchase-orders',
            'update-purchase-orders',
            'delete-purchase-orders',

            // Compras
            'create-purchases',
            'read-purchases',
            'update-purchases',
            'delete-purchases',

            // Clientes
            'create-customers',
            'read-customers',
            'update-customers',
            'delete-customers',

            // Cotizaciones
            'create-quotes',
            'read-quotes',
            'update-quotes',
            'delete-quotes',

            // Ventas
            'create-sales',
            'read-sales',
            'update-sales',
            'delete-sales',

            // Movimientos
            'create-movements',
            'read-movements',
            'update-movements',
            'delete-movements',

            // Transferencias
            'create-transfers',
            'read-transfers',
            'update-transfers',
            'delete-transfers',

            // Reportes
            'read-top-products',
            'read-top-customers',
            'read-low-stock',

            // Usuarios
            'create-users',
            'read-users',
            'update-users',
            'delete-users',

            // Roles
            'create-roles',
            'read-roles',
            'update-roles',
            'delete-roles',

            // Ajustes
            'read-settings',
            'update-settings',

            // Compañías
            'create-companies',
            'read-companies',
            'update-companies',
            'delete-companies',

            // Facturas
            'read-invoices',
            'send-invoices',
            'xml-invoices',
            'pdf-invoices',

            // Notas de crédito
            'read-note',
            'send-note',
            'xml-note',
            'pdf-note',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());

        Role::create(['name' => 'editor'])->givePermissionTo([
            // Categorias
            'create-categories',
            'read-categories',
            'update-categories',
            'delete-categories',

            // Productos
            'create-products',
            'read-products',
            'update-products',
            'delete-products',

            // Almacenes
            'create-warehouses',
            'read-warehouses',
            'update-warehouses',
            'delete-warehouses',

            // Proveedores
            'create-suppliers',
            'read-suppliers',
            'update-suppliers',
            'delete-suppliers',

            // Clientes
            'create-customers',
            'read-customers',
            'update-customers',
            'delete-customers',
        ]);

        Role::create(['name' => 'visitante'])->givePermissionTo([
            // Categorias
            'read-categories',

            // Productos
            'read-products',

            // Almacenes
            'read-warehouses',

            // Proveedores
            'read-suppliers',

            // Ordenes de compra
            'read-purchase-orders',

            // Compras
            'read-purchases',

            // Clientes
            'read-customers',

            // Clientes
            'read-customers',

            // Cotizaciones
            'read-quotes',

            // Ventas
            'read-sales',

            // Movimientos
            'read-movements',

            // Transferencias
            'read-transfers',

            // Reportes
            'read-top-products',
            'read-top-customers',
            'read-low-stock',

            // Usuarios
            'read-users',

            // Roles
            'read-roles',

            // Ajustes
            'read-settings',

            // Compañías
            'read-companies',

            // Facturas
            'read-invoices',

            // Notas de crédito
            'read-note',
        ]);

        User::factory()->create([
            'name' => 'Jair Vasquez',
            'email' => 'jair1624@gmail.com',
            'password' => bcrypt('12345678')
        ])->assignRole('admin');
    }
}
