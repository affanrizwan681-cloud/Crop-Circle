<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'unit',
        'stock_quantity',
        'weekly_quota',
        'image',
        'status',
        'is_moderated',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'weekly_quota' => 'integer',
            'is_moderated' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(5);
            }
        });
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('sort_order');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(ProductAvailability::class, 'product_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id')->where('is_moderated', true);
    }

    public function activeAd(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProductAd::class)->where('status', 'approved')->orderBy('sort_order');
    }

    public function effectivePrice(): float
    {
        return (float) ($this->activeAd?->discount_price ?? $this->price);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'product_id');
    }

    public function scopeAvailable($query)
    {
        return $query->visible()
            ->where('status', 'available')
            ->where('stock_quantity', '>', 0);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_moderated', true)
            ->whereHas('farmer', function ($farmer) {
                $farmer->where('status', 'active')
                    ->whereHas('farmerProfile', function ($profile) {
                        $profile->where('is_approved', true);
                    });
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeModerated($query)
    {
        return $query->where('is_moderated', true);
    }

    public function averageRating(): float
    {
        return round((float) $this->reviews()->avg('rating'), 1) ?: 5.0;
    }

    public function reviewCount(): int
    {
        return $this->reviews()->count();
    }

    public function isAvailableOnDay(string $dayOfWeek): bool
    {
        $avail = $this->availabilities()->where('day_of_week', $dayOfWeek)->first();
        if ($avail) {
            return $avail->is_available && ($avail->available_quantity > $avail->reserved_quantity);
        }
        return $this->status === 'available' && $this->stock_quantity > 0;
    }

    public function getAvailableStockForDay(string $dayOfWeek): int
    {
        $avail = $this->availabilities()->where('day_of_week', $dayOfWeek)->first();
        if ($avail) {
            return max(0, $avail->available_quantity - $avail->reserved_quantity);
        }
        return max(0, $this->stock_quantity);
    }

    public function deductStock(int $quantity, ?string $dayOfWeek = null): bool
    {
        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->decrement('stock_quantity', $quantity);

        if ($this->stock_quantity <= 0) {
            $this->update(['status' => 'sold_out']);
        }

        if ($dayOfWeek) {
            $avail = $this->availabilities()->where('day_of_week', $dayOfWeek)->first();
            if ($avail) {
                $avail->increment('reserved_quantity', $quantity);
            }
        }

        return true;
    }

    public function restoreStock(int $quantity, ?string $dayOfWeek = null): void
    {
        $this->increment('stock_quantity', $quantity);
        if ($this->status === 'sold_out') {
            $this->update(['status' => 'available']);
        }

        if ($dayOfWeek) {
            $avail = $this->availabilities()->where('day_of_week', $dayOfWeek)->first();
            if ($avail) {
                $avail->decrement('reserved_quantity', min($quantity, $avail->reserved_quantity));
            }
        }
    }

    public function releaseReservation(int $quantity, ?string $dayOfWeek = null): void
    {
        if (!$dayOfWeek) {
            return;
        }

        $availability = $this->availabilities()
            ->where('day_of_week', $dayOfWeek)
            ->lockForUpdate()
            ->first();

        if ($availability && $availability->reserved_quantity > 0) {
            $availability->decrement('reserved_quantity', min($quantity, $availability->reserved_quantity));
        }
    }
}
