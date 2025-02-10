<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Order #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            font-size: 14px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .invoice-info {
            margin-bottom: 20px;
        }

        .invoice-info p {
            margin: 5px 0;
        }

        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .status-completed {
            background-color: #28a745;
            color: white;
        }

        .status-pending {
            background-color: #ffc107;
            color: black;
        }

        .status-canceled {
            background-color: #dc3545;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .total {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Invoice</h1>
        <p>Order #: {{ $invoice->id }}</p>
    </div>

    <div class="invoice-info">
        <p><strong>User:</strong> {{ $invoice->user->name }}</p>
        <p><strong>Status:</strong> 
            <span class="status status-{{ $invoice->status }}">
                {{ ucfirst($invoice->status) }}
            </span>
        </p>
        <p><strong>Created At:</strong> {{ $invoice->created_at->format('d-M-Y H:i A') }}</p>
    </div>

    <h3>Order Items</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->orderItems as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->menuItem->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Total Price: ${{ number_format($invoice->total_price, 2) }}</p>

</body>
</html>
