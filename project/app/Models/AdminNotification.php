<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use Modeling;
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', 1);
    }

    public function scopeUnRead($query)
    {
        return $query->where('is_read', 0);
    }
}
