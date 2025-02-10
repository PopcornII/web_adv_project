@extends('layouts.index')

@section('content')
<div class="container">
    <h1 class="mb-4">Invoice List</h1>

    <!-- Search & Filter -->
    <form action="{{ route('invoices.index') }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search invoice..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-secondary">Search</button>
            </div>
        </div>
    </form>

    <!-- Invoice Table -->
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice ID</th>
                        <th>User</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $invoice->id }}</td>
                            <td>{{ $invoice->user->name }}</td>
                            <td>${{ number_format($invoice->total_price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $invoice->status === 'completed' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td>{{ $invoice->created_at->format('d-M-Y') }}</td>
                            <td>
                                 <!-- View Invoice Button (Trigger Modal) -->
                                <button type="button" class="btn btn-sm btn-info" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#invoiceModal" 
                                    onclick="loadInvoice({{ $invoice->id }})">
                                    View
                                </button>
                                <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-sm btn-success">PDF</a>
                                <form action="{{ route('invoices.updateStatus', $invoice->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-sm btn-primary">Paid</button>
                                </form>
                                <form action="{{ route('invoices.updateStatus', $invoice->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="canceled">
                                    <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $invoices->links() }}
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalLabel">Invoice Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="invoiceDetails">
                <p>Loading...</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to load invoice details in modal
    function loadInvoice(invoiceId) {
    fetch(`/invoices/${invoiceId}/show`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('invoiceDetails').innerHTML = data;
        })
        .catch(error => console.error('Error loading invoice:', error));
}
</script>
@endsection
