<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Feed extends Model
{
    use Modeling;

    protected $appends = [
        'is_like',
        'like_users',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'body',
        'media_path',
        'media_type',
        'link',
        'event_date',
        'privacy',
        'likes_count',
        'comments_count',
        'shares_count',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(FeedLike::class);
    }

    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'feed_likes')->withTimestamps();
    }

    public function getIsLikeAttribute()
    {
        $userId = Auth::id();

        if (!$userId) {
            return false;
        }

        if ($this->relationLoaded('likedByUsers')) {
            return $this->likedByUsers->contains('id', $userId);
        }

        return $this->likedByUsers()->where('users.id', $userId)->exists();
    }

    public function getLikeUsersAttribute()
    {
        if ($this->relationLoaded('likedByUsers')) {
            return $this->likedByUsers->map(function ($user) {
                return [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'image' => $user->image,
                ];
            })->values();
        }

        return $this->likedByUsers()
            ->select('users.id', 'users.name', 'users.image')
            ->get()
            ->map(function ($user) {
                return [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'image' => $user->image,
                ];
            })
            ->values();
    }

    public function scopeVisibleTo($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('privacy', 'public')->orWhere('user_id', $userId);
        });
    }
}
