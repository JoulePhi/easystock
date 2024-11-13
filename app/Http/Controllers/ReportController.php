<?php

namespace App\Http\Controllers;

use App\Exports\FinancialExport;
use App\Exports\InventoryExport;
use App\Exports\SalesExport;
use App\Models\Order;
use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Reports/Index');
    }

    public function sales(Request $request)
    {
        $query = Order::with(['items', 'payment'])
            ->where('status', 'completed')
            ->when($request->date_range, function ($query, $dateRange) {
                $dates = explode(' to ', $dateRange);
                $query->whereBetween('created_at', $dates);
            });

        // Group by date
        $dailySales = $query->get()->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function ($orders) {
            return [
                'total_orders' => $orders->count(),
                'total_sales' => $orders->sum('total_amount'),
                'total_tax' => $orders->sum('tax'),
                'total_items' => $orders->sum(function ($order) {
                    return $order->items->sum('quantity');
                })
            ];
        });

        // Get top selling items
        $topItems = DB::table('order_items')
            ->join('items', 'order_items.item_id', '=', 'items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select(
                'items.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_price) as total_sales')
            )
            ->groupBy('items.id', 'items.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Payment methods breakdown
        $paymentMethods = DB::table('payment_transactions')
            ->join('orders', 'payment_transactions.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('payment_method')
            ->get();

        return Inertia::render('Reports/Sales', [
            'dailySales' => $dailySales,
            'topItems' => $topItems,
            'paymentMethods' => $paymentMethods,
            'dateRange' => $request->date_range
        ]);
    }

    public function inventory(Request $request)
    {
        $items = Item::with(['category', 'vendor'])
            ->when($request->category, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->get();

        // Stock movement analysis
        $stockMovements = StockTransaction::with(['item', 'type'])
            ->when($request->date_range, function ($query, $dateRange) {
                $dates = explode(' to ', $dateRange);
                $query->whereBetween('created_at', $dates);
            })
            ->get()
            ->groupBy('item_id')
            ->map(function ($transactions) {
                return [
                    'incoming' => $transactions->where('quantity', '>', 0)->sum('quantity'),
                    'outgoing' => abs($transactions->where('quantity', '<', 0)->sum('quantity')),
                    'net_movement' => $transactions->sum('quantity')
                ];
            });

        // Low stock alerts
        $lowStockItems = Item::where('current_stock', '<=', DB::raw('minimum_stock'))
            ->with(['category', 'vendor'])
            ->get();

        // Stock value
        $stockValue = $items->sum(function ($item) {
            return $item->current_stock * $item->cost;
        });

        return Inertia::render('Reports/Inventory', [
            'items' => $items,
            'stockMovements' => $stockMovements,
            'lowStockItems' => $lowStockItems,
            'stockValue' => $stockValue,
            'dateRange' => $request->date_range
        ]);
    }

    public function financial(Request $request)
    {
        $startDate = $request->date_range ?
            Carbon::parse(explode(' to ', $request->date_range)[0]) :
            Carbon::now()->startOfMonth();

        $endDate = $request->date_range ?
            Carbon::parse(explode(' to ', $request->date_range)[1]) :
            Carbon::now();

        // Revenue
        $revenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Cost of goods sold
        $cogs = StockTransaction::where('quantity', '<', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum(DB::raw('ABS(quantity) * unit_price'));

        // Gross profit
        $grossProfit = $revenue - $cogs;

        // Daily revenue breakdown
        $dailyRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })
            ->map(function ($orders) {
                return $orders->sum('total_amount');
            });

        // Category performance
        $categoryPerformance = DB::table('order_items')
            ->join('items', 'order_items.item_id', '=', 'items.id')
            ->join('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'item_categories.name',
                DB::raw('SUM(order_items.total_price) as revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('item_categories.id', 'item_categories.name')
            ->get();

        return Inertia::render('Reports/Financial', [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'grossProfit' => $grossProfit,
            'dailyRevenue' => $dailyRevenue,
            'categoryPerformance' => $categoryPerformance,
            'dateRange' => $request->date_range
        ]);
    }

    public function exportSales(Request $request)
    {
        return Excel::download(new SalesExport($request->date_range), 'sales-report.xlsx');
    }

    public function exportInventory(Request $request)
    {
        return Excel::download(new InventoryExport(), 'inventory-report.xlsx');
    }

    public function exportFinancial(Request $request)
    {
        return Excel::download(new FinancialExport($request->date_range), 'financial-report.xlsx');
    }
}
