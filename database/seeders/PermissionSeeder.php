<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            'view_dashboard',

            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Role Management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',

            // Inventory Management
            'view_inventory',
            'create_items',
            'edit_items',
            'delete_items',
            'adjust_stock',
            'view_stock_history',

            // Category Management
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',

            // Vendor Management
            'view_vendors',
            'create_vendors',
            'edit_vendors',
            'delete_vendors',

            // Purchase Order Management
            'view_purchase_orders',
            'create_purchase_orders',
            'edit_purchase_orders',
            'delete_purchase_orders',
            'approve_purchase_orders',

            // Sales/Order Management
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            'process_payments',
            'void_orders',

            // Reports
            'view_sales_reports',
            'view_inventory_reports',
            'view_purchase_reports',
            'export_reports',

            // Settings
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
