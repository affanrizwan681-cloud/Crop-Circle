<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PickupSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'market_id',
        'day_of_week',
        'start_time',
        'end_time',
        'cutoff_time',
        'max_orders',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_orders' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'pickup_slot_id');
    }

    public function getFormattedWindowAttribute(): string
    {
        return date('g:i A', strtotime($this->start_time)) . ' - ' . date('g:i A', strtotime($this->end_time));
    }

    public function hasCapacityForDate(string $date): bool
    {
        $ordersCount = $this->orders()
            ->where('pickup_date', $date)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->count();

        return $ordersCount < $this->max_orders;
    }
}
