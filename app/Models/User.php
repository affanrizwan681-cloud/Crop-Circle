<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'role',
        'status',
        'avatar',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isFarmer(): bool
    {
        return $this->role === 'farmer';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isApprovedFarmer(): bool
    {
        return $this->isFarmer() && $this->farmerProfile && $this->farmerProfile->is_approved;
    }

    public function farmerProfile(): HasOne
    {
        return $this->hasOne(FarmerProfile::class, 'user_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'farmer_id');
    }

    public function pickupSlots(): HasMany
    {
        return $this->hasMany(PickupSlot::class, 'farmer_id');
    }

    public function customerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function farmerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'farmer_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class, 'customer_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'customer_id');
    }

    public function customerReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function farmerReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'farmer_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id')->latest();
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function conversations(): HasMany
    {
        return $this->isFarmer()
            ? $this->hasMany(Conversation::class, 'farmer_id')
            : $this->hasMany(Conversation::class, 'customer_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function averageRating(): float
    {
        return round((float) $this->farmerReviews()->where('is_moderated', true)->avg('rating'), 1) ?: 5.0;
    }

    public function reviewCount(): int
    {
        return $this->farmerReviews()->where('is_moderated', true)->count();
    }
}
