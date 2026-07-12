<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportDepartment extends Model
{
    protected $fillable = ['name', 'status'];

    /* relations */
    public function tickets()
    {
        return $this->hasMany(SupportTicket::class, 'department_id');
    }

    /* scopes */
    public function scopeActive($q)
    {
        return $q->where('status', 1);
    }

    public function scopeSearching($q, array $cols = ['name'])
    {
        $search = request('search');
        if (!$search) return $q;

        return $q->where(function ($w) use ($cols, $search) {
            foreach ($cols as $c) $w->orWhere($c, 'like', "%{$search}%");
        });
    }

    /* accessors */
    public function getStatusBadgeAttribute()
    {
        return $this->status
            ? '<span class="badge badge-success">'.__('Active').'</span>'
            : '<span class="badge badge-danger">'.__('Inactive').'</span>';
    }
}
