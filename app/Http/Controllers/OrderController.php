<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Item;
use App\Models\TransactionType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payment'])
            ->when($request->search, function ($query, $search) {
                $query->where('order_number', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(
                $request->date_range,
                function ($query, $dateRange) {
                    $dates = explode(' to ', $dateRange);
                    $query->whereBetween('created_at', $dates);
                }
            );

        $orders = $query->latest()->paginate(10);

        return Inertia::render('Sales/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status', 'date_range'])
        ]);
    }

    public function create()
    {
        $items = Item::where('is_active', true)
            ->where('current_stock', '>', 0)
            ->with('category')
            ->get()
            ->groupBy('category.name');

        return Inertia::render('Sales/Create', [
            'items' => $items,
            'tax_rate' => config('app.tax_rate', 0.1) // 10% tax
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,card',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Create order
                $subtotal = 0;
                $orderItems = [];

                foreach ($validated['items'] as $item) {
                    $inventoryItem = Item::findOrFail($item['item_id']);

                    // Check stock
                    if ($inventoryItem->current_stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$inventoryItem->name}");
                    }

                    $total = $inventoryItem->price * $item['quantity'];
                    $subtotal += $total;

                    $orderItems[] = [
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $inventoryItem->price,
                        'total_price' => $total
                    ];

                    // Reduce stock
                    $inventoryItem->decrement('current_stock', $item['quantity']);

                    // Create stock transaction
                    $inventoryItem->stockTransactions()->create([
                        'user_id' => auth()->id(),
                        'type_id' => TransactionType::where('code', 'SAL')->first()->id,
                        'quantity' => -$item['quantity'],
                        'unit_price' => $inventoryItem->price,
                        'notes' => "Sale deduction"
                    ]);
                }

                $tax = $subtotal * config('app.tax_rate', 0.1);
                $total = $subtotal + $tax;

                $order = Order::create([
                    'order_number' => 'ORD-' . date('Ymd') . '-' . random_int(1000, 9999),
                    'user_id' => auth()->id(),
                    'status' => 'completed',
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total_amount' => $total,
                    'notes' => $validated['notes'] ?? null
                ]);

                // Create order items
                $order->items()->createMany($orderItems);

                // Create payment
                $order->payment()->create([
                    'amount' => $validated['amount_paid'],
                    'payment_method' => $validated['payment_method'],
                    'status' => 'completed'
                ]);
            });

            return redirect()->route('sales.index')
                ->with('success', 'Order created successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['items.item', 'payment', 'user']);

        return Inertia::render('Sales/Show', [
            'order' => $order
        ]);
    }

    public function void(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                // Return items to stock
                foreach ($order->items as $item) {
                    $item->item->increment('current_stock', $item->quantity);

                    // Create stock transaction
                    $item->item->stockTransactions()->create([
                        'user_id' => auth()->id(),
                        'type_id' => TransactionType::where('code', 'RET')->first()->id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'notes' => "Order void return"
                    ]);
                }

                // Update order status
                $order->update(['status' => 'cancelled']);

                // Update payment status if exists
                if ($order->payment) {
                    $order->payment->update(['status' => 'refunded']);
                }
            });

            return back()->with('success', 'Order voided successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to void order');
        }
    }

    public function receipt(Order $order)
    {
        $order->load(['items.item', 'payment', 'user']);

        $pdf = Pdf::loadView('pdfs.receipt', [
            'order' => $order
        ]);

        return $pdf->download("Receipt-{$order->order_number}.pdf");
    }
}
