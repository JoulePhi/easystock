<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Purchase', 'code' => 'PUR'],
            ['name' => 'Sales', 'code' => 'SAL'],
            ['name' => 'Stock Adjustment (Add)', 'code' => 'ADJ+'],
            ['name' => 'Stock Adjustment (Reduce)', 'code' => 'ADJ-'],
            ['name' => 'Stock Return', 'code' => 'RET'],
            ['name' => 'Stock Transfer', 'code' => 'TRF'],
            ['name' => 'Stock Loss', 'code' => 'LOSS'],
            ['name' => 'Stock Take', 'code' => 'TAKE']
        ];

        foreach ($types as $type) {
            TransactionType::create($type);
        }
    }
}
