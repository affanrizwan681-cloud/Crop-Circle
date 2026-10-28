@extends('layouts.app')

@section('title', 'Add New Product — Farmer Portal')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4 p-md-5 border-0 shadow-sm">
                <a href="{{ route('farmer.products.index') }}" class="text-success text-decoration-none small fw-bold mb-2 d-inline-block">
                    &larr; Back to Products List
                </a>
                <h3 class="fw-bold mb-2">Add New Harvest or Product</h3>
                <p class="text-muted small mb-4">New listings are reviewed by the CropCircle team before customers can see or order them.</p>

                @if($errors->any())
                    <div class="alert alert-danger border-0 small py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('farmer.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Product / Crop Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="e.g. Organic Honeycrisp Apples">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Description & Harvest Notes <span class="text-danger">*</span></label>
                            <textarea name="description" rows="3" class="form-control" required placeholder="Describe flavor profile, harvest date, organic practices, storage tips...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control" required value="{{ old('price') }}" placeholder="4.50">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Unit of Measurement <span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control" required value="{{ old('unit', 'kg') }}" placeholder="e.g. kg, bunch, piece, jar, basket">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Current Available Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock_quantity" class="form-control" required value="{{ old('stock_quantity', 20) }}" min="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Weekly Quota / Harvest Target</label>
                            <input type="number" name="weekly_quota" class="form-control" value="{{ old('weekly_quota', 50) }}" min="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Main Product Photo</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <input type="url" name="image_url" class="form-control mt-2" placeholder="Or paste main photo URL (https://...)" value="{{ old('image_url') }}">@error('image_url')<small class="text-danger">{{ $message }}</small>@enderror
                            <input type="url" name="image_url" class="form-control mt-2" placeholder="Or paste main photo URL (https://...)" value="{{ old('image_url') }}">@error('image_url')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Additional Gallery Photos (Multiple)</label>
                            <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                            <input type="url" name="gallery_image_url" class="form-control mt-2" placeholder="Or paste one gallery photo URL (https://...)" value="{{ old('gallery_image_url') }}">@error('gallery_image_url')<small class="text-danger">{{ $message }}</small>@enderror
                            <input type="url" name="gallery_image_url" class="form-control mt-2" placeholder="Or paste one gallery photo URL (https://...)" value="{{ old('gallery_image_url') }}">@error('gallery_image_url')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="isFeatured">
                                    Feature this harvest on the homepage
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-cropcircle px-4 fw-bold">
                                Submit for Review &rarr;
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
