<!-- resources/views/pdfs/receipt.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
        }

        .receipt-info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .total-section {
            text-align: right;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">EasyStock Restaurant</div>
        <div>123 Restaurant Street</div>
        <div>City, State, ZIP</div>
        <div>Phone: (555) 123-4567</div>
    </div>

    <div class="receipt-info">
        <div>Receipt #: {{ $order->order_number }}</div>
        <div>Date: {{ $order->created_at->format('M d, Y h:i A') }}</div>
        <div>Cashier: {{ $order->user->name }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->item->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div>Subtotal: ${{ number_format($order->subtotal, 2) }}</div>
        <div>Tax: ${{ number_format($order->tax, 2) }}</div>
        <div style="font-weight: bold; font-size: 14px;">
            Total: ${{ number_format($order->total_amount, 2) }}
        </div>
        @if ($order->payment)
            <div style="margin-top: 10px;">
                Paid via: {{ ucfirst($order->payment->payment_method) }}
                @if ($order->payment->payment_method === 'cash')
                    <div>
                        Amount Paid: ${{ number_format($order->payment->amount_paid, 2) }}<br>
                        Change: ${{ number_format($order->payment->amount_paid - $order->total_amount, 2) }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="footer">
        Thank you for your business!<br>
        Please come again
    </div>
</body>

</html>
