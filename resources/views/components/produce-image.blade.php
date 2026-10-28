@props(['image' => null, 'category' => null, 'alt' => 'Fresh local product', 'class' => '', 'style' => '', 'id' => null])

@php
    $categoryLabel = is_object($category) ? ($category->slug ?? $category->name ?? '') : (string) $category;
    $categorySlug = \Illuminate\Support\Str::slug($categoryLabel);
    $imageByCategory = [
        'vegetables' => 'photo-1566385101042-1a0aa0c1268c',
        'vegetable' => 'photo-1566385101042-1a0aa0c1268c',
        'fruits' => 'photo-1560806887-1e4cd0b6cbd6',
        'fruit' => 'photo-1560806887-1e4cd0b6cbd6',
        'dairy' => 'photo-1488477181946-6428a0291777',
        'bakery' => 'photo-1509440159596-0249088772ff',
        'bread' => 'photo-1509440159596-0249088772ff',
        'meat' => 'photo-1607623814075-e51df1bdc82f',
        'honey' => 'photo-1587049352851-8d4e89133924',
        'flowers' => 'photo-1490750967868-88aa4486c946',
        'herbs' => 'photo-1501004318641-b39e6451bec6',
        'pantry' => 'photo-1490645935967-10de6ba17061',
    ];
    $photoId = collect($imageByCategory)->first(fn ($value, $key) => str_contains($categorySlug, $key));
    $photoId = $photoId ?: 'photo-1490645935967-10de6ba17061';
    $fallbackUrl = 'https://images.unsplash.com/' . $photoId . '?auto=format&fit=crop&w=1000&q=85';
    $imageUrl = $image ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://']) ? $image : asset('storage/' . $image)) : $fallbackUrl;
@endphp

<img
    @if($id) id="{{ $id }}" @endif
    src="{{ $imageUrl }}"
    alt="{{ $alt }}"
    class="{{ $class }}"
    @if($style) style="{{ $style }}" @endif
    loading="lazy"
    onerror="this.onerror=null;this.src='{{ $fallbackUrl }}';"
>