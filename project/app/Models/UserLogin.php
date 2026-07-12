<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    use Modeling;

    protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
