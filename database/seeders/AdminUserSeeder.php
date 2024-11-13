<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@easystock.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'Super Admin')->first()->id
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@easystock.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id
        ]);
    }
}
