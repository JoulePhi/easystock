<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Raw Materials',
                'description' => 'Basic ingredients and raw materials'
            ],
            [
                'name' => 'Spices & Seasonings',
                'description' => 'Various spices and seasonings'
            ],
            [
                'name' => 'Beverages',
                'description' => 'Drinks and beverage ingredients'
            ],
            [
                'name' => 'Packaging',
                'description' => 'Packaging materials and containers'
            ],
            [
                'name' => 'Cleaning Supplies',
                'description' => 'Cleaning and sanitation materials'
            ],
            [
                'name' => 'Kitchen Equipment',
                'description' => 'Kitchen tools and equipment'
            ]
        ];

        foreach ($categories as $category) {
            ItemCategory::create($category);
        }
    }
}
