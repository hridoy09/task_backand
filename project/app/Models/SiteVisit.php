<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    use Modeling;
    
    protected $fillable = [
        'ip',
        'visit_date'
    ];

    protected $casts = [
        'visit_date' => 'datetime',
    ];
}
