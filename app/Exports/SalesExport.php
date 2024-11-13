<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class SalesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $dateRange;

    public function __construct($dateRange = null)
    {
        $this->dateRange = $dateRange;
    }

    public function query()
    {
        $query = Order::query()
            ->with(['items.item', 'payment', 'user'])
            ->where('status', 'completed');

        if ($this->dateRange) {
            $dates = explode(' to ', $this->dateRange);
            $query->whereBetween('created_at', $dates);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Date',
            'Cashier',
            'Items',
            'Subtotal',
            'Tax',
            'Total',
            'Payment Method',
            'Status'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->created_at->format('Y-m-d H:i:s'),
            $order->user->name,
            $order->items->map(function ($item) {
                return "{$item->item->name} x {$item->quantity}";
            })->implode(', '),
            $order->subtotal,
            $order->tax,
            $order->total_amount,
            $order->payment ? ucfirst($order->payment->payment_method) : 'N/A',
            ucfirst($order->status)
        ];
    }
}
