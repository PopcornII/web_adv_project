<div>
    <h5>Invoice ID: #{{ $invoice->id }}</h5>
    <p><strong>User:</strong> {{ $invoice->user->name }}</p>
    <p><strong>Status:</strong> 
        <span class="badge bg-{{ $invoice->status === 'completed' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }}">
            {{ ucfirst($invoice->status) }}
        </span>
    </p>
    <p><strong>Total Price:</strong> ${{ number_format($invoice->total_price, 2) }}</p>
    <p><strong>Created At:</strong> {{ $invoice->created_at->format('d-M-Y H:i A') }}</p>

    <hr>

    <h5>Order Items</h5>
    <table class="table table-bordered">
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

    <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-success">Download PDF</a>
</div>
