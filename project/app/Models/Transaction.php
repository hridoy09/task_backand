<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use Modeling;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCategory($q, ?string $category)
    {
        if ($category !== null && $category !== '') {
            $q->where('category', $category);
        }
        return $q;
    }
}
