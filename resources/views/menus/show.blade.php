<div class="modal fade" id="viewModal-{{ $menuItem->id }}" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Menu Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($menuItem->image)
                    <img src="{{ asset('storage/' . $menuItem->image) }}" class="img-fluid mb-3" alt="{{ $menuItem->name }}" style="with:100px height: 100px border-radius: 10px; border: 1px solid #ddd;">
                @endif
                <p><strong>Name:</strong> {{ $menuItem->name }}</p>
                <p><strong>Description:</strong> {{ $menuItem->description }}</p>
                <p><strong>Price:</strong> ${{ number_format($menuItem->price, 2) }}</p>
                <p><strong>Category:</strong> {{ $menuItem->category }}</p>
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
