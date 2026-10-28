@extends('layouts.app') @section('title','Product ads | CropCircle') @section('content')
<div class="container py-4"><h1 class="h2">Product ads</h1><p class="text-muted">Create an offer for an approved product. Admin approval is required before it appears.</p>@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div class="card mb-4"><div class="card-header"><h2 class="h3 mb-0">Create an ad</h2></div><div class="card-body"><form method="POST" action="{{ route('farmer.ads.store') }}" enctype="multipart/form-data">@csrf<div class="row g-3"><div class="col-md-6"><label class="form-label">Approved product</label><select class="form-select" name="product_id" required><option value="">Choose a product</option>@foreach($products as $p)<option value="{{ $p->id }}" data-image="{{ $p->image ? (\Illuminate\Support\Str::startsWith($p->image,['http://','https://']) ? $p->image : (\Illuminate\Support\Str::startsWith($p->image,['http://','https://']) ? $p->image : asset('storage/'.$p->image))) : '' }}" @selected(old('product_id')==$p->id)>{{ $p->name }} â€” {{ number_format($p->price,2) }} / {{ $p->unit }}</option>@endforeach</select>@error('product_id')<small class="text-danger">{{ $message }}</small>@enderror</div><div class="col-md-6"><label class="form-label">Ad headline</label><input class="form-control" name="title" maxlength="160" value="{{ old('title') }}" required></div><div class="col-md-6"><label class="form-label">Discounted offer price</label><input class="form-control" name="discount_price" type="number" min="0.01" step="0.01" value="{{ old('discount_price') }}" required><div class="form-hint">Must be lower than the product price. Approved offers apply at checkout.</div>@error('discount_price')<small class="text-danger">{{ $message }}</small>@enderror</div><div class="col-md-6"><label class="form-label">Ad image</label><div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="use-product-image" name="use_product_image" value="1" @checked(old('use_product_image'))><label class="form-check-label" for="use-product-image">Use this selected productâ€™s image</label></div>@error('use_product_image')<small class="text-danger d-block">{{ $message }}</small>@enderror<div class="mb-2"><label class="form-label" for="ad-image-upload">Or upload an image</label><input class="form-control" id="ad-image-upload" name="image" type="file" accept="image/*">@error('image')<small class="text-danger">{{ $message }}</small>@enderror</div><div><label class="form-label" for="ad-image-url">Or paste an image URL</label><input class="form-control" id="ad-image-url" name="image_url" type="url" inputmode="url" placeholder="https://example.com/product-photo.jpg" value="{{ old('image_url') }}">@error('image_url')<small class="text-danger">{{ $message }}</small>@enderror</div><div id="ad-image-preview-wrap" class="mt-2 d-none"><img id="ad-image-preview" src="" alt="Ad image preview" class="rounded border" style="width:180px;height:120px;object-fit:cover"><div id="ad-image-caption" class="form-hint mt-1"></div></div></div><div class="col-12"><label class="form-label">Ad details</label><textarea class="form-control" name="description" maxlength="1200" rows="3" required>{{ old('description') }}</textarea></div><div class="col-12"><button class="btn btn-success">Send ad for approval</button></div></div></form></div></div>
<h2 class="h3">Your submitted ads</h2><div class="row g-3">@forelse($ads as $ad)<div class="col-md-6 col-xl-4"><div class="card h-100"><img src="{{ $ad->display_image }}" class="card-img-top" style="height:190px;object-fit:cover" alt="{{ $ad->title }}"><div class="card-body"><div class="d-flex justify-content-between"><h3 class="h4">{{ $ad->title }}</h3><span class="badge bg-secondary-lt">{{ str_replace('_',' ',ucfirst($ad->status)) }}</span></div><p class="text-muted">{{ $ad->product?->name }}</p><p>{{ $ad->description }}</p><strong>{{ number_format($ad->discount_price,2) }}</strong> <del class="text-muted">{{ number_format($ad->original_price,2) }}</del>@if($ad->admin_note)<p class="small">Admin note: {{ $ad->admin_note }}</p>@endif</div>@if(in_array($ad->status,['pending','approved']))<div class="card-footer"><form method="POST" action="{{ route('farmer.ads.removalRequest',$ad) }}">@csrf<button class="btn btn-outline-danger btn-sm">Request ad removal</button></form></div>@endif</div></div>@empty<div class="col-12 text-muted">No ads yet. Create your first product promotion above.</div>@endforelse</div><div class="mt-3">{{ $ads->links() }}</div></div>@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const product = document.querySelector('select[name="product_id"]');
    const upload = document.getElementById('ad-image-upload');
    const wrap = document.getElementById('ad-image-preview-wrap');
    const preview = document.getElementById('ad-image-preview');
    const caption = document.getElementById('ad-image-caption');
    const useProduct = document.getElementById('use-product-image');
    const imageUrl = document.getElementById('ad-image-url');
    let productImage = '';
    function showImage(src, label) {
        if (!src) { wrap.classList.add('d-none'); preview.removeAttribute('src'); return; }
        preview.src = src; caption.textContent = label; wrap.classList.remove('d-none');
    }
    function updateProductImage() {
        productImage = product.selectedOptions[0]?.dataset.image || '';
        if (useProduct.checked) showImage(productImage, productImage ? 'Using the selected product photo' : 'This product has no photo. Choose an upload or image URL.');
        else if (upload.files.length) showImage(URL.createObjectURL(upload.files[0]), 'Using your uploaded ad image');
        else if (imageUrl.value.trim()) showImage(imageUrl.value.trim(), 'Using the image URL');
        else { wrap.classList.add('d-none'); preview.removeAttribute('src'); }
    }
    product.addEventListener('change', updateProductImage);
    useProduct.addEventListener('change', function () { if (useProduct.checked) { upload.value = ''; imageUrl.value = ''; } updateProductImage(); });
    imageUrl.addEventListener('input', updateProductImage);
    upload.addEventListener('change', function () {
        if (upload.files[0]) showImage(URL.createObjectURL(upload.files[0]), 'Using your uploaded ad image');
        else showImage(productImage, productImage ? 'Using the selected product photo' : 'Upload an ad image to continue.');
    });
    updateProductImage();
});
</script>
@endpush
@endsection