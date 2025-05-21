<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderDetails.product')->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|json',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric|min:0',
            'final_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card'
        ]);

        $items = json_decode($request->items, true);

        $order = Order::create([
            'order_number' => 'ORD-' . Str::random(8),
            'total_amount' => $request->total_amount,
            'discount_amount' => $request->discount_amount,
            'final_amount' => $request->final_amount,
            'payment_method' => $request->payment_method,
            'status' => 'completed'
        ]);

        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);

            if ($product->stock < $item['quantity']) {
                $order->delete();
                return back()->with('error', "Insufficient stock for {$product->name}");
            }

            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => $item['discount'] ?? 0,
                'subtotal' => $item['subtotal']
            ]);

            // Update product stock
            $product->update(['stock' => $product->stock - $item['quantity']]);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load('orderDetails.product');
        return view('orders.show', compact('order'));
    }

    public function print(Order $order)
    {
        $order->load('orderDetails.product');
        return view('orders.print', compact('order'));
    }
}