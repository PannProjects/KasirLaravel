@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Order Details</h5>
        <div>
            <a href="{{ route('orders.print', $order) }}" class="btn btn-secondary" target="_blank">Print Receipt</a>
            <a href="{{ route('orders.index') }}" class="btn btn-primary">Back to Orders</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Order Information</h6>
                <table class="table table-sm">
                    <tr>
                        <th>Order Number:</th>
                        <td>{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <th>Date:</th>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Payment Method:</th>
                        <td>{{ ucfirst($order->payment_method) }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td><span class="badge bg-success">{{ ucfirst($order->status) }}</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <h6>Order Items</h6>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Discount</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderDetails as $detail)
                    <tr>
                        <td>{{ $detail->product->name }}</td>
                        <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>Rp {{ number_format($detail->discount, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                        <td><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total Discount:</strong></td>
                        <td><strong>Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Final Amount:</strong></td>
                        <td><strong>Rp {{ number_format($order->final_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection