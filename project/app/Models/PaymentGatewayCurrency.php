<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayCurrency extends Model
{
    protected $fillable = [
        'payment_gateway_id',
        'currency_code',
        'rate',
        'is_default',
    ];

    protected $casts = [
        'rate' => 'float',
        'is_default' => 'boolean',
    ];

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }
}
