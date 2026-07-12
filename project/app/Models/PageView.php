<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    use Modeling;
    
    protected $fillable = [
        'slug',
        'views'
    ];
}
