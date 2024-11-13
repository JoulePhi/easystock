<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dateRange;

    public function __construct($dateRange = null)
    {
        $this->dateRange = $dateRange;
    }

    public function collection()
    {
        $startDate = $this->dateRange ?
            Carbon::parse(explode(' to ', $this->dateRange)[0]) :
            Carbon::now()->startOfMonth();

        $endDate = $this->dateRange ?
            Carbon::parse(explode(' to ', $this->dateRange)[1]) :
            Carbon::now();

        return Order::with(['items.item', 'payment'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Order Number',
            'Revenue',
            'COGS',
            'Gross Profit',
            'Margin %',
            'Items Sold',
            'Payment Method'
        ];
    }

    public function map($order): array
    {
        $cogs = $order->items->sum(function ($item) {
            return $item->quantity * $item->item->cost;
        });

        $grossProfit = $order->total_amount - $cogs;
        $margin = ($grossProfit / $order->total_amount) * 100;

        return [
            $order->created_at->format('Y-m-d'),
            $order->order_number,
            $order->total_amount,
            $cogs,
            $grossProfit,
            round($margin, 2),
            $order->items->sum('quantity'),
            $order->payment ? ucfirst($order->payment->payment_method) : 'N/A'
        ];
    }
}
