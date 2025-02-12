@extends('layouts.index')

@section('content')
    <div class="container">
        <h2 class="font-bold mt-2">Menu Items</h2>

        <!-- Success and Error Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <!-- Search Form -->
            <form action="{{ route('menus.index') }}" method="GET" class="mb-3 d-flex gap-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search menu..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-outline-primary">Search</button>
            </form>

                <!-- Category Filter Buttons -->
            <div class="mb-3">
                <a href="{{ route('menus.index') }}" class="btn {{ request('category') ? 'btn-outline-primary' : 'btn-primary' }}">
                    All
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('menus.index', ['category' => $category]) }}" 
                    class="btn {{ request('category') == $category ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ ucfirst($category) }}
                    </a>
                @endforeach
            </div>


            <!-- Add New Menu Item Button -->
            <a href="{{ route('menus.create') }}" class="btn btn-primary mb-3">Add New</a>
        </div>

        <div class='card'>
            <div class='card-body'>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menuItems as $index => $menuItem)
                            <tr>
                                <td>{{ $menuItems->firstItem() + $index }}</td>
                                <td>
                                    @if($menuItem->image)
                                        <img src="{{ asset('storage/' . $menuItem->image) }}" alt="{{ $menuItem->name }}" 
                                        style="width: 75px; height: 75px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd;">
                                    @else
                                        <span>No Image</span>
                                    @endif
                                </td>
                                <td>{{ $menuItem->name }}</td>
                                <td>{{ $menuItem->description }}</td>
                                <td>${{ number_format($menuItem->price, 2) }}</td>
                                <td>{{ ucfirst($menuItem->category) }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal-{{ $menuItem->id }}">View</button>
                                    <a href="{{ route('menus.edit', $menuItem->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('menus.destroy', $menuItem->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- View Detail Modal -->
                            <div class="modal fade" id="viewModal-{{ $menuItem->id }}" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel">Menu Item Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if($menuItem->image)
                                                <img src="{{ asset('storage/' . $menuItem->image) }}" class="img-fluid mb-3" alt="{{ $menuItem->name }}"
                                                 style="width: 100%; height: 250px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd;">
                                            @endif
                                            <p><strong>Name:</strong> {{ $menuItem->name }}</p>
                                            <p><strong>Description:</strong> {{ $menuItem->description }}</p>
                                            <p><strong>Price:</strong> ${{ number_format($menuItem->price, 2) }}</p>
                                            <p><strong>Category:</strong> {{ ucfirst($menuItem->category) }}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ route('menus.edit', $menuItem->id) }}" class="btn btn-warning">Edit</a>
                                            <form action="{{ route('menus.destroy', $menuItem->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End View Detail Modal -->

                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No menu items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div>
            {{ $menuItems->appends(['search' => request('search'), 'category' => request('category')])->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
