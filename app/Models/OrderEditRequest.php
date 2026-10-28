<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEditRequest extends Model
{
    public const STATUS_PENDING_FARMER = 'pending_farmer';
    public const STATUS_FARMER_ACCEPTED = 'farmer_accepted';
    public const STATUS_FARMER_DENIED = 'farmer_denied';
    public const STATUS_CUSTOMER_PROCEEDED = 'customer_proceeded';
    public const STATUS_CUSTOMER_CANCELLED = 'customer_cancelled';
    public const STATUS_ORDER_CANCELLED = 'order_cancelled';

    protected $fillable = ['order_id', 'customer_id', 'farmer_id', 'proposed_items', 'proposed_notes', 'status', 'farmer_response', 'responded_at', 'customer_decided_at'];

    protected function casts(): array
    {
        return ['proposed_items' => 'array', 'responded_at' => 'datetime', 'customer_decided_at' => 'datetime'];
    }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function farmer(): BelongsTo { return $this->belongsTo(User::class, 'farmer_id'); }
}