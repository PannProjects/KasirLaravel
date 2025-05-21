@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create New Order</h5>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                        @csrf

                        <!-- Product Selection -->
                        <div class="mb-4">
                            <label class="form-label">Add Products</label>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <select class="form-select" id="productSelect">
                                        <option value="">Select a product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                    data-price="{{ $product->price }}"
                                                    data-stock="{{ $product->stock }}"
                                                    data-discount="{{ $product->discounts->where('is_active', true)->first()?->percentage ?? 0 }}">
                                                {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control" id="quantity" min="1" value="1" placeholder="Quantity">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary w-100" id="addProduct">Add</button>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items Table -->
                        <div class="table-responsive mb-4">
                            <table class="table" id="orderItems">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Discount</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                                        <td><strong id="totalAmount">Rp 0</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total Discount:</strong></td>
                                        <td><strong id="discountAmount">Rp 0</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Final Amount:</strong></td>
                                        <td><strong id="finalAmount">Rp 0</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-4">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                            </select>
                        </div>

                        <!-- Hidden Inputs -->
                        <input type="hidden" name="items" id="orderItemsInput">
                        <input type="hidden" name="total_amount" id="totalAmountInput">
                        <input type="hidden" name="discount_amount" id="discountAmountInput">
                        <input type="hidden" name="final_amount" id="finalAmountInput">

                        <div class="text-end">
                            <button type="submit" class="btn btn-success" id="submitOrder" disabled>Complete Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let orderItems = [];

document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('productSelect');
    const quantityInput = document.getElementById('quantity');
    const addProductBtn = document.getElementById('addProduct');
    const orderItemsTable = document.getElementById('orderItems').getElementsByTagName('tbody')[0];
    const orderItemsInput = document.getElementById('orderItemsInput');
    const totalAmountElement = document.getElementById('totalAmount');
    const discountAmountElement = document.getElementById('discountAmount');
    const finalAmountElement = document.getElementById('finalAmount');
    const totalAmountInput = document.getElementById('totalAmountInput');
    const discountAmountInput = document.getElementById('discountAmountInput');
    const finalAmountInput = document.getElementById('finalAmountInput');
    const submitOrderBtn = document.getElementById('submitOrder');

    function updateOrderSummary() {
        const totalAmount = orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const totalDiscount = orderItems.reduce((sum, item) => sum + (item.discount * item.quantity), 0);
        const finalAmount = totalAmount - totalDiscount;

        totalAmountElement.textContent = `Rp ${totalAmount.toLocaleString('id-ID')}`;
        discountAmountElement.textContent = `Rp ${totalDiscount.toLocaleString('id-ID')}`;
        finalAmountElement.textContent = `Rp ${finalAmount.toLocaleString('id-ID')}`;

        totalAmountInput.value = totalAmount;
        discountAmountInput.value = totalDiscount;
        finalAmountInput.value = finalAmount;

        orderItemsInput.value = JSON.stringify(orderItems);
        submitOrderBtn.disabled = orderItems.length === 0;
    }

    function addOrderItem(productId, name, price, quantity, discountPercentage) {
        const existingItem = orderItems.find(item => item.product_id === productId);

        if (existingItem) {
            existingItem.quantity += quantity;
            existingItem.subtotal = (existingItem.price * existingItem.quantity) - (existingItem.discount * existingItem.quantity);
        } else {
            const discount = (price * discountPercentage) / 100;
            orderItems.push({
                product_id: productId,
                name: name,
                price: price,
                quantity: quantity,
                discount: discount,
                subtotal: (price * quantity) - (discount * quantity)
            });
        }

        renderOrderItems();
        updateOrderSummary();
    }

    function renderOrderItems() {
        orderItemsTable.innerHTML = '';
        orderItems.forEach((item) => {
            const row = orderItemsTable.insertRow();
            row.innerHTML = `
                <td>${item.name}</td>
                <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                <td>${item.quantity}</td>
                <td>Rp ${(item.discount * item.quantity).toLocaleString('id-ID')}</td>
                <td>Rp ${item.subtotal.toLocaleString('id-ID')}</td>
            `;
        });
    }

    addProductBtn.addEventListener('click', function() {
        const selectedOption = productSelect.selectedOptions[0];
        if (!selectedOption.value) return;

        const productId = selectedOption.value;
        const name = selectedOption.text.split(' - ')[0];
        const price = parseFloat(selectedOption.dataset.price);
        const stock = parseInt(selectedOption.dataset.stock);
        const discountPercentage = parseFloat(selectedOption.dataset.discount);
        const quantity = parseInt(quantityInput.value);

        if (quantity > stock) {
            alert('Quantity exceeds available stock!');
            return;
        }

        addOrderItem(productId, name, price, quantity, discountPercentage);
        productSelect.value = '';
        quantityInput.value = 1;
    });

    // Initialize
    updateOrderSummary();
});
</script>
@endpush
@endsection
