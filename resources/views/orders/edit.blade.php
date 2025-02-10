@extends('layouts.index')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Order</h1>

    <!-- Success and Error Messages -->
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Left Side: Invoice Layout -->
            <div class="col-md-6">
                <h3>Order Details</h3>
                <div class="card">
                    <div class="card-body">
                        <div id="invoice-items">
                            @foreach($order->orderItems as $orderItem)
                                <div class="border p-2 mb-2 d-flex justify-content-between align-items-center">
                                    <span>{{ $orderItem->menuItem->name }} (x{{ $orderItem->quantity }}) - ${{ number_format($orderItem->menuItem->price * $orderItem->quantity, 2) }}</span>
                                    <input type="number" name="order_items[{{ $orderItem->id }}][quantity]" value="{{ $orderItem->quantity }}" class="form-control w-25" min="1">
                                    <input type="hidden" name="order_items[{{ $orderItem->id }}][id]" value="{{ $orderItem->id }}">
                                </div>
                            @endforeach
                        </div>
                        <h6 class="mt-3">Total Price: ${{ number_format($order->total_price, 2) }}</h6>
                    </div>
                </div>
            </div>

            <!-- Right Side: Menu List (Optional) -->
            <div class="col-md-6">
                <h3>Menu Items</h3>
                <div class="row">
                    @foreach($menuItems as $menuItem)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <img src="{{ asset('storage/' . $menuItem->image) }}" alt="{{ $menuItem->name }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h6 class="card-title">{{ $menuItem->name }}</h6>
                                <p class="card-text">${{ number_format($menuItem->price, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Update Order</button>
        </div>
    </form>
</div>
@endsection
