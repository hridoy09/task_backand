<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use Modeling;

    protected $guarded = [];
    
    public function blogs()
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }
}
