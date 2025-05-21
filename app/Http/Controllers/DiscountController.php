<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::with('product')->latest()->paginate(10);
        return view('discounts.index', compact('discounts'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('discounts.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        // Convert is_active to boolean
        $validated['is_active'] = $request->boolean('is_active');

        Discount::create($validated);
        return redirect()->route('discounts.index')
            ->with('success', 'Discount created successfully.');
    }

    public function edit(Discount $discount)
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('discounts.edit', compact('discount', 'products'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        // Convert is_active to boolean
        $validated['is_active'] = $request->boolean('is_active');

        $discount->update($validated);
        return redirect()->route('discounts.index')
            ->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('discounts.index')
            ->with('success', 'Discount deleted successfully.');
    }
}