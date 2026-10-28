<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteFeedback extends Model
{
    use HasFactory;

    protected $table = 'website_feedback';

    protected $fillable = ['user_id', 'submitter_role', 'type', 'subject', 'message', 'rating', 'status', 'public_consent', 'is_featured'];

    protected function casts(): array
    {
        return ['rating' => 'integer', 'public_consent' => 'boolean', 'is_featured' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFeaturedForHomepage(Builder $query): Builder
    {
        return $query
            ->where('type', 'review')
            ->where('status', 'reviewed')
            ->where('public_consent', true)
            ->where('is_featured', true)
            ->whereIn('submitter_role', ['customer', 'farmer'])
            ->whereHas('user', fn (Builder $user) => $user
                ->whereColumn('users.role', 'website_feedback.submitter_role')
                ->where('users.status', 'active'));
    }
}