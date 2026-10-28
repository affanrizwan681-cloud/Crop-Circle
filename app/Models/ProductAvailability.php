<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'day_of_week',
        'available_quantity',
        'reserved_quantity',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'available_quantity' => 'integer',
            'reserved_quantity' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function remainingQuantity(): int
    {
        return max(0, $this->available_quantity - $this->reserved_quantity);
    }
}
