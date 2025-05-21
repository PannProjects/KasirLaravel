<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:products',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ]);

        Product::create($request->all());
        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:products,code,' . $product->id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ]);

        $product->update($request->all());
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Check if product has any active discounts
        if ($product->discounts()->where('is_active', true)->exists()) {
            return redirect()->route('products.index')
                ->with('error', 'Cannot delete product with active discounts. Please deactivate discounts first.');
        }

        // Check if product has any order details
        if ($product->orderDetails()->exists()) {
            $product->delete(); // This will perform a soft delete
            return redirect()->route('products.index')
                ->with('success', 'Product has been soft deleted successfully.');
        }

        $product->forceDelete(); // This will perform a hard delete
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}