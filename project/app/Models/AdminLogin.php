<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class AdminLogin extends Model
{
    use Modeling;
    
    protected $fillable = [
        'admin_id',
        'session_id',
        'device_type',
        'browser',
        'os',
        'ip',
        'country',
        'city',
    ];
    
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
