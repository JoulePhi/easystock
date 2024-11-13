<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Item::query()
            ->with(['category', 'vendor']);
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Name',
            'Category',
            'Current Stock',
            'Minimum Stock',
            'Unit',
            'Cost',
            'Value',
            'Vendor',
            'Status'
        ];
    }

    public function map($item): array
    {
        return [
            $item->sku,
            $item->name,
            $item->category->name,
            $item->current_stock,
            $item->minimum_stock,
            $item->unit,
            $item->cost,
            $item->current_stock * $item->cost,
            $item->vendor->name,
            $item->is_active ? 'Active' : 'Inactive'
        ];
    }
}
