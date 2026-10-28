<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'farmer_id',
        'product_id',
        'order_id',
        'rating',
        'comment',
        'farmer_reply',
        'replied_at',
        'is_moderated',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_moderated' => 'boolean',
            'replied_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function scopeModerated($query)
    {
        return $query->where('is_moderated', true);
    }
}
