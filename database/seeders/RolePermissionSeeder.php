<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin - memiliki semua permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        $allPermissions = Permission::all();
        $superAdmin->permissions()->attach($allPermissions->pluck('id'));

        // Admin
        $admin = Role::where('name', 'Admin')->first();
        $adminPermissions = Permission::whereIn('name', [
            'view_dashboard',
            'view_users',
            'create_users',
            'edit_users',
            'view_inventory',
            'create_items',
            'edit_items',
            'adjust_stock',
            'view_stock_history',
            'view_categories',
            'create_categories',
            'edit_categories',
            'view_vendors',
            'create_vendors',
            'edit_vendors',
            'view_purchase_orders',
            'create_purchase_orders',
            'edit_purchase_orders',
            'view_orders',
            'create_orders',
            'edit_orders',
            'process_payments',
            'view_sales_reports',
            'view_inventory_reports',
            'view_purchase_reports',
            'export_reports'
        ])->get();
        $admin->permissions()->attach($adminPermissions->pluck('id'));

        // Manager
        $manager = Role::where('name', 'Manager')->first();
        $managerPermissions = Permission::whereIn('name', [
            'view_dashboard',
            'view_inventory',
            'view_stock_history',
            'view_purchase_orders',
            'create_purchase_orders',
            'approve_purchase_orders',
            'view_orders',
            'view_sales_reports',
            'view_inventory_reports',
            'view_purchase_reports',
            'export_reports'
        ])->get();
        $manager->permissions()->attach($managerPermissions->pluck('id'));

        // Cashier
        $cashier = Role::where('name', 'Cashier')->first();
        $cashierPermissions = Permission::whereIn('name', [
            'view_dashboard',
            'view_inventory',
            'view_orders',
            'create_orders',
            'process_payments'
        ])->get();
        $cashier->permissions()->attach($cashierPermissions->pluck('id'));

        // Inventory Staff
        $inventoryStaff = Role::where('name', 'Inventory Staff')->first();
        $inventoryPermissions = Permission::whereIn('name', [
            'view_dashboard',
            'view_inventory',
            'adjust_stock',
            'view_stock_history',
            'view_purchase_orders',
            'create_purchase_orders',
            'view_inventory_reports'
        ])->get();
        $inventoryStaff->permissions()->attach($inventoryPermissions->pluck('id'));

        // Kitchen Staff
        $kitchenStaff = Role::where('name', 'Kitchen Staff')->first();
        $kitchenPermissions = Permission::whereIn('name', [
            'view_dashboard',
            'view_inventory',
            'view_orders'
        ])->get();
        $kitchenStaff->permissions()->attach($kitchenPermissions->pluck('id'));
    }
}
