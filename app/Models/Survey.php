<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public const DUPLICATE_USER_ONLY = 'user_only';
    public const DUPLICATE_IP_ONLY = 'ip_only';
    public const DUPLICATE_COOKIE_ONLY = 'cookie_only';
    public const DUPLICATE_NONE = 'none';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'is_active',
        'public_token',
        'published_at',
        'closed_at',
        'duplicate_policy',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopePublishedForPublic(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $builder) {
                $builder->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null && $this->closed_at->isPast();
    }

    public function acceptsResponses(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && ($this->published_at === null || $this->published_at->lte(now()))
            && ! $this->isClosed();
    }
}
