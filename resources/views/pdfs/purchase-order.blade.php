<!-- resources/views/pdfs/purchase-order.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Purchase Order {{ $po->po_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .company-info {
            margin-bottom: 30px;
        }

        .vendor-info {
            margin-bottom: 30px;
        }

        .po-info {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Purchase Order</h1>
    </div>

    <div class="company-info">
        <h3>From:</h3>
        <p>EasyStock Restaurant</p>
        <p>123 Restaurant Street</p>
        <p>City, State, ZIP</p>
        <p>Phone: (555) 123-4567</p>
    </div>

    <div class="vendor-info">
        <h3>To:</h3>
        <p>{{ $po->vendor->name }}</p>
        <p>{{ $po->vendor->address }}</p>
        <p>Contact: {{ $po->vendor->contact_person }}</p>
        <p>Phone: {{ $po->vendor->phone }}</p>
        <p>Email: {{ $po->vendor->email }}</p>
    </div>

    <div class="po-info">
        <p><strong>PO Number:</strong> {{ $po->po_number }}</p>
        <p><strong>Date:</strong> {{ $po->created_at->format('F d, Y') }}</p>
        <p><strong>Expected Date:</strong> {{ $po->expected_date->format('F d, Y') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($po->status) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>SKU</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($po->items as $item)
                <tr>
                    <td>{{ $item->item->name }}</td>
                    <td>{{ $item->item->sku }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->item->unit }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p>Total Amount: ${{ number_format($po->total_amount, 2) }}</p>
    </div>

    @if ($po->notes)
        <div class="notes">
            <h3>Notes:</h3>
            <p>{{ $po->notes }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Please confirm receipt of this purchase order and expected delivery date.</p>
        <p>If you have any questions about this purchase order, please contact:</p>
        <p>{{ $po->user->name }} - {{ $po->user->email }}</p>
    </div>
</body>

</html>
