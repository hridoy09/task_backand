<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use Modeling;
    
    protected $fillable = [
        'key',
        'value'
    ];

    protected $casts = [
        'value' => 'array'
    ];
}
