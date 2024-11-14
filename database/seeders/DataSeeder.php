<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('item_categories')->insert([
            [
                'name' => 'Bahan Baku',
                'description' => 'Bahan utama untuk memasak',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bumbu',
                'description' => 'Bumbu dan rempah-rempah',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Minuman',
                'description' => 'Bahan minuman',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Kemasan',
                'description' => 'Peralatan pengemasan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
        DB::table('vendors')->insert([
            [
                'name' => 'PT Supplier Utama',
                'email' => 'supplier@example.com',
                'phone' => '08123456789',
                'address' => 'Jl. Supplier No. 1, Jakarta',
                'contact_person' => 'John Supplier',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'CV Fresh Food',
                'email' => 'fresh@example.com',
                'phone' => '08234567890',
                'address' => 'Jl. Fresh No. 2, Jakarta',
                'contact_person' => 'Jane Fresh',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'UD Packaging',
                'email' => 'pack@example.com',
                'phone' => '08345678901',
                'address' => 'Jl. Pack No. 3, Jakarta',
                'contact_person' => 'Bob Pack',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        DB::table('items')->insert([
            [
                'name' => 'Beras',
                'description' => 'Beras premium',
                'sku' => 'BR001',
                'category_id' => 1,
                'unit' => 'kg',
                'minimum_stock' => 50,
                'current_stock' => 100,
                'price' => 15000,
                'cost' => 12000,
                'vendor_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Garam',
                'description' => 'Garam beryodium',
                'sku' => 'GM001',
                'category_id' => 2,
                'unit' => 'kg',
                'minimum_stock' => 10,
                'current_stock' => 20,
                'price' => 5000,
                'cost' => 4000,
                'vendor_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sirup',
                'description' => 'Sirup rasa jeruk',
                'sku' => 'SR001',
                'category_id' => 3,
                'unit' => 'botol',
                'minimum_stock' => 20,
                'current_stock' => 30,
                'price' => 25000,
                'cost' => 20000,
                'vendor_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Box Makanan',
                'description' => 'Kotak makanan ukuran medium',
                'sku' => 'BX001',
                'category_id' => 4,
                'unit' => 'pcs',
                'minimum_stock' => 100,
                'current_stock' => 200,
                'price' => 2000,
                'cost' => 1500,
                'vendor_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
