<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FarmerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'farm_name',
        'contact_person',
        'description',
        'address',
        'city',
        'state',
        'postal_code',
        'latitude',
        'longitude',
        'operating_days',
        'pickup_start_time',
        'pickup_end_time',
        'profile_image',
        'is_approved',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'operating_days' => 'array',
            'is_approved' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function markets(): BelongsToMany
    {
        return $this->belongsToMany(Market::class, 'market_farmer')
            ->withPivot('assigned_stall', 'status')
            ->withTimestamps();
    }

    public function approvedMarkets(): BelongsToMany
    {
        return $this->markets()->wherePivot('status', 'approved');
    }
}
