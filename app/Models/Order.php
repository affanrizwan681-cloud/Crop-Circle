<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_READY = 'ready_for_pickup';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'order_number',
        'parent_order_id',
        'customer_id',
        'farmer_id',
        'market_id',
        'pickup_slot_id',
        'pickup_date',
        'pickup_time',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'customer_name',
        'customer_phone',
        'notes',
        'decline_reason',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'pickup_date' => 'date',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ML-' . strtoupper(Str::random(3)) . '-' . date('Ymd') . '-' . rand(1000, 9999);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id');
    }

    public function pickupSlot(): BelongsTo
    {
        return $this->belongsTo(PickupSlot::class, 'pickup_slot_id');
    }

    public function parentOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'parent_order_id');
    }

    public function subOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'parent_order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'order_id');
    }

    public function customerEditDeadline(): \Illuminate\Support\Carbon
    {
        $pickupTime = $this->pickupSlot?->start_time ?? trim(explode('-', $this->pickup_time)[0]);
        return \Illuminate\Support\Carbon::parse($this->pickup_date->toDateString().' '.$pickupTime)->subDay();
    }

    public function canBeEditedByCustomer(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_ACCEPTED, self::STATUS_READY], true)
            && now()->lessThanOrEqualTo($this->customerEditDeadline());
    }

    public function editRequests(): HasMany
    {
        return $this->hasMany(OrderEditRequest::class, 'order_id')->latest();
    }

    public function canBeCancelledByCustomer(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeAcceptedByFarmer(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeMarkedReadyByFarmer(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_ACCEPTED, self::STATUS_READY]);
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_ACCEPTED]);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-warning text-dark',
            self::STATUS_ACCEPTED => 'bg-info text-dark',
            self::STATUS_READY => 'bg-primary text-white',
            self::STATUS_COMPLETED => 'bg-success text-white',
            self::STATUS_CANCELLED => 'bg-secondary text-white',
            self::STATUS_REJECTED => 'bg-danger text-white',
            default => 'bg-light text-dark',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_ACCEPTED => 'Accepted / Preparing',
            self::STATUS_READY => 'Ready for Pickup',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REJECTED => 'Declined',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function restoreInventory(): void
    {
        $dayOfWeek = $this->pickup_date ? $this->pickup_date->format('l') : null;
        foreach ($this->items as $item) {
            if ($item->product) {
                $item->product->restoreStock($item->quantity, $dayOfWeek);
            }
        }
    }

    public function releaseReservations(): void
    {
        $dayOfWeek = $this->pickup_date ? $this->pickup_date->format('l') : null;
        foreach ($this->items as $item) {
            if ($item->product) {
                $item->product->releaseReservation($item->quantity, $dayOfWeek);
            }
        }
    }
}
