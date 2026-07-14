<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use Modeling;

    protected $fillable = [
        'user_id',
        'type',
        'body',
        'media_path',
        'media_type',
        'link',
        'event_date',
        'privacy',
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

    public function scopeVisibleTo($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('privacy', 'public')->orWhere('user_id', $userId);
        });
    }
}
