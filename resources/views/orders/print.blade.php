<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .receipt {
            max-width: 300px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            margin: 5px 0;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .item {
            margin: 5px 0;
        }
        .item-name {
            float: left;
        }
        .item-price {
            float: right;
        }
        .clear {
            clear: both;
        }
        .total {
            margin-top: 10px;
        }
        .total-line {
            display: flex;
            justify-content: space-between;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>Kasir System</h1>
            <p>Receipt</p>
            <p>{{ $order->order_number }}</p>
            <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="divider"></div>

        @foreach($order->orderDetails as $detail)
        <div class="item">
            <div class="item-name">
                {{ $detail->product->name }}<br>
                {{ $detail->quantity }} x Rp {{ number_format($detail->price, 0, ',', '.') }}
            </div>
            <div class="item-price">
                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
            </div>
            <div class="clear"></div>
        </div>
        @endforeach

        <div class="divider"></div>

        <div class="total">
            <div class="total-line">
                <span>Total Amount:</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-line">
                <span>Discount:</span>
                <span>Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-line">
                <strong>Final Amount:</strong>
                <strong>Rp {{ number_format($order->final_amount, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <p>Payment Method: {{ ucfirst($order->payment_method) }}</p>
            <p>Thank you for your purchase!</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Receipt</button>
    </div>
</body>
</html>