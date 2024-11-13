<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'vendor'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category_id', $category);
            });

        $items = $query->latest()->paginate(10);

        return Inertia::render('Inventory/Index', [
            'items' => $items,
            'filters' => $request->only(['search', 'category']),
            'categories' => ItemCategory::select('id', 'name')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Inventory/Create', [
            'categories' => ItemCategory::select('id', 'name')->get(),
            'vendors' => Vendor::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:items,sku',
            'category_id' => 'required|exists:item_categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'unit' => 'required|string|max:50',
            'minimum_stock' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Item::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Item created successfully.');
    }

    public function edit(Item $item)
    {
        return Inertia::render('Inventory/Edit', [
            'item' => $item,
            'categories' => ItemCategory::select('id', 'name')->get(),
            'vendors' => Vendor::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:items,sku,' . $item->id,
            'category_id' => 'required|exists:item_categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'unit' => 'required|string|max:50',
            'minimum_stock' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Item deleted successfully.');
    }
}
