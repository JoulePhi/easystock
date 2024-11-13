<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\TransactionType;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class StockManagementController extends Controller
{
    public function index(Request $request)
    {
        $transactions = StockTransaction::with(['item', 'type', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('item', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type_id', $type);
            })
            ->latest()
            ->paginate(15);

        return Inertia::render('Inventory/Stock/Index', [
            'transactions' => $transactions,
            'transactionTypes' => TransactionType::all(),
            'filters' => $request->only(['search', 'type'])
        ]);
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'type_id' => 'required|exists:transaction_types,id',
            'quantity' => 'required|numeric|not_in:0',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Create stock transaction
                $transaction = StockTransaction::create([
                    'item_id' => $validated['item_id'],
                    'user_id' => auth()->id(),
                    'type_id' => $validated['type_id'],
                    'quantity' => $validated['quantity'],
                    'unit_price' => $validated['unit_price'],
                    'notes' => $validated['notes']
                ]);

                // Update item stock
                $item = Item::find($validated['item_id']);
                $item->current_stock += $validated['quantity'];
                $item->save();

                // Check if stock is below minimum
                if ($item->current_stock <= $item->minimum_stock) {
                    $users = User::permission('view_inventory')->get();
                    Notification::send($users, new LowStockAlert($item));

                    // Buat stock alert
                    $item->stockAlerts()->create([
                        'threshold_quantity' => $item->minimum_stock,
                        'status' => 'active'
                    ]);
                }
            });

            return back()->with('success', 'Stock adjusted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to adjust stock');
        }
    }

    public function history(Item $item)
    {
        $transactions = $item->stockTransactions()
            ->with(['type', 'user'])
            ->latest()
            ->get();

        return response()->json($transactions);
    }
}
