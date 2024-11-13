<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Admin',
            'Manager',
            'Cashier',
            'Inventory Staff',
            'Kitchen Staff'
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
