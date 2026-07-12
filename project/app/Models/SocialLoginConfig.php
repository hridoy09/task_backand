<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class SocialLoginConfig extends Model
{
    use Modeling;
    
    protected $casts = ['config' => 'array'];
    
    protected $fillable = ['*'];
}
