<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use Modeling;
    
    protected $fillable = [
        'user_id',
        'status',
        'amount',
        'currency',
        'method',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status == 'pending') {
            return '<span class="badge text-bg-dark">' . __('Pending') . '</span>';
        }

        if ($this->status == 'success') {
            return getBadge('Success','success');
        }


        if ($this->status == 'failed') {
            return getBadge('Failed','danger');
        }

        if ($this->status == 'refunded') {
            return getBadge('Refunded','warning');
        }
    }

    public function scopeStatus($q, ?string $status)
    {
        if ($status !== null && $status !== '') {
            $q->where('status', $status);
        }
        return $q;
    }

    public function scopeFailed($q)
    {
        return $q->where('status', 'failed');
    }
    
    public function scopeSuccessful($q) {
        return $q->where('status','success');
    }

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    
}
