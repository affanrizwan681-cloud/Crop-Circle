<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'address',
        'description',
        'latitude',
        'longitude',
        'operating_days',
        'open_time',
        'close_time',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'operating_days' => 'array',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function farmerProfiles(): BelongsToMany
    {
        return $this->belongsToMany(FarmerProfile::class, 'market_farmer')
            ->withPivot('assigned_stall', 'status')
            ->withTimestamps();
    }

    public function approvedFarmers(): BelongsToMany
    {
        return $this->farmerProfiles()->wherePivot('status', 'approved')->where('is_approved', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'market_id');
    }

    public function pickupSlots(): HasMany
    {
        return $this->hasMany(PickupSlot::class, 'market_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
