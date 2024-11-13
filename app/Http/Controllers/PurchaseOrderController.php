<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\TransactionType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['vendor', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where('po_number', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            });

        $purchaseOrders = $query->latest()->paginate(10);

        return Inertia::render('PurchaseOrders/Index', [
            'purchaseOrders' => $purchaseOrders,
            'filters' => $request->only(['search', 'status']),
            'statuses' => PurchaseOrder::STATUSES
        ]);
    }

    public function create()
    {
        return Inertia::render('PurchaseOrders/Create', [
            'vendors' => Vendor::where('is_active', true)->get(),
            'items' => Item::where('is_active', true)->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'expected_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $po = PurchaseOrder::create([
                    'vendor_id' => $validated['vendor_id'],
                    'user_id' => auth()->id(),
                    'po_number' => 'PO-' . date('Ymd') . '-' . random_int(1000, 9999),
                    'status' => 'draft',
                    'expected_date' => $validated['expected_date'],
                    'notes' => $validated['notes'],
                    'total_amount' => 0
                ]);

                $totalAmount = 0;
                foreach ($validated['items'] as $item) {
                    $totalPrice = $item['quantity'] * $item['unit_price'];
                    $totalAmount += $totalPrice;

                    $po->items()->create([
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $totalPrice
                    ]);
                }

                $po->update(['total_amount' => $totalAmount]);
            });

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase Order created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create Purchase Order');
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['vendor', 'user', 'items.item']);

        return Inertia::render('PurchaseOrders/Show', [
            'purchaseOrder' => $purchaseOrder
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'sent'])) {
            return back()->with('error', 'Cannot update this Purchase Order');
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,sent,received,cancelled',
            'expected_date' => 'required|date',
            'received_date' => 'nullable|date|required_if:status,received',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($purchaseOrder, $validated) {
                $purchaseOrder->update($validated);

                // If status changed to received, update stock
                if ($validated['status'] === 'received' && $purchaseOrder->status !== 'received') {
                    foreach ($purchaseOrder->items as $item) {
                        $item->item->increment('current_stock', $item->quantity);

                        // Create stock transaction
                        $item->item->stockTransactions()->create([
                            'user_id' => auth()->id(),
                            'type_id' => TransactionType::where('code', 'PUR')->first()->id,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'notes' => "Received from PO: {$purchaseOrder->po_number}"
                        ]);
                    }
                }
            });

            return back()->with('success', 'Purchase Order updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update Purchase Order');
        }
    }

    public function generatePdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['vendor', 'user', 'items.item']);

        $pdf = PDF::loadView('pdfs.purchase-order', [
            'po' => $purchaseOrder
        ]);

        return $pdf->download("PO-{$purchaseOrder->po_number}.pdf");
    }
}
