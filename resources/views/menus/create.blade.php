@extends('layouts.index')

@section('content')
    <div class="container">
        <h1 class="mb-4">Create New Menu Item</h1>

        <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                @error('description')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="text" name="price" id="price" class="form-control 
                    @error('price') is-invalid @enderror" value="{{ old('price') }}" required
                    pattern="^\d+(\.\d{1,2})?$" title="Enter a valid positive decimal number" 
                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                <div class="invalid-feedback">
                    @error('price')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category" class="form-control" required>
                    @foreach(['Fried', 'Soup', 'Steam', 'Dessert', 'Drink', 'Smoothies'] as $category)
                        <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mt-4">
                <label for="image">Upload Image</label>
                <input type="file" name="image" id="image" class="form-control-file" onchange="previewImage(event)" required>
                @error('image')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <div class="mt-2">
                    <img id="imagePreview" src="#" alt="Image Preview" style="display:none; max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                </div>
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-primary me-2">Save</button>
                <a href="{{ route('menus.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('imagePreview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
