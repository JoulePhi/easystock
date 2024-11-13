<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemCategoryController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::withCount('items')
            ->latest()
            ->paginate(10);

        return Inertia::render('Inventory/Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:item_categories,name',
            'description' => 'nullable|string'
        ]);

        ItemCategory::create($validated);

        return back()->with('success', 'Category created successfully');
    }

    public function update(Request $request, ItemCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:item_categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $category->update($validated);

        return back()->with('success', 'Category updated successfully');
    }

    public function destroy(ItemCategory $category)
    {
        if ($category->items()->exists()) {
            return back()->with('error', 'Cannot delete category with existing items');
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully');
    }
}
