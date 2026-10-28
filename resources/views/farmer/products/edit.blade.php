@extends('layouts.app')

@section('title', 'Edit ' . $product->name . ' — Farmer Portal')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4 p-md-5 border-0 shadow-sm">
                <a href="{{ route('farmer.products.index') }}" class="text-success text-decoration-none small fw-bold mb-2 d-inline-block">
                    &larr; Back to Products List
                </a>
                <h3 class="fw-bold mb-4">Edit Product: {{ $product->name }}</h3>

                <form action="{{ route('farmer.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Product / Crop Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $product->name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Description & Harvest Notes <span class="text-danger">*</span></label>
                            <textarea name="description" rows="3" class="form-control" required>{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control" required value="{{ old('price', $product->price) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Unit <span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control" required value="{{ old('unit', $product->unit) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="stock_quantity" class="form-control" required value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select">
                                <option value="available" {{ old('status', $product->status) === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="sold_out" {{ old('status', $product->status) === 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                                <option value="unavailable" {{ old('status', $product->status) === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Replace Main Photo</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <input type="url" name="image_url" class="form-control mt-2" placeholder="Or paste a replacement photo URL (https://...)" value="{{ old('image_url') }}">@error('image_url')<small class="text-danger">{{ $message }}</small>@enderror
                            <input type="url" name="image_url" class="form-control mt-2" placeholder="Or paste a replacement photo URL (https://...)" value="{{ old('image_url') }}">@error('image_url')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Add Additional Gallery Photos</label>
                            <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                            <input type="url" name="gallery_image_url" class="form-control mt-2" placeholder="Or paste one gallery photo URL (https://...)" value="{{ old('gallery_image_url') }}">@error('gallery_image_url')<small class="text-danger">{{ $message }}</small>@enderror
                            <input type="url" name="gallery_image_url" class="form-control mt-2" placeholder="Or paste one gallery photo URL (https://...)" value="{{ old('gallery_image_url') }}">@error('gallery_image_url')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="isFeatured">
                                    Feature this harvest on the homepage
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-cropcircle px-4 fw-bold">
                                Save Product Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
