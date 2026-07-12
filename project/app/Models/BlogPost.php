<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $casts = [
        'seo_content' => 'object'
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 1);
    }

    public function scopeUnpublished($query)
    {
        return $query->where('status', 0);
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status == 1) {
            return '<span class="badge badge-success">' . __('Published') . '</span>';
        } else {
            return '<span class="badge badge-secondary">' . __('Unpublished') . '</span>';
        }
    }

    public function category() {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }
}
