<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use Modeling;
    
    protected $casts = [
        'sections' => 'array',
        'seo_content' => 'array',
    ];

    /** scope for privacy page */
    public function scopePrivacy($query)
    {
        return $query->where('privacy', 1);
    }

    public function getIsDefaultBadgeAttribute()
    {
        if ($this->is_default) {
            return '<span class="badge badge-success">' . __('Default') . '</span>';
        } else {
            return '<span class="badge badge-secondary">' . __('No') . '</span>';
        }
    }

    public function getPrivacyPageBadgeAttribute()
    {
        if ($this->privacy) {
            return '<span class="badge badge-success">' . __('Yes') . '</span>';
        } else {
            return '<span class="badge badge-secondary">' . __('No') . '</span>';
        }
    }
}
