<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentGateway extends Model
{
    public function __construct(array $attributes = [])
    {
    }
    
    protected $casts = [
        'config' => 'object'
    ];

    protected $appends = ['image_url'];

    protected $fillable = ['name', 'key', 'config', 'image'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeAutomatic($query)
    {
        return $query->where('manual', 0);
    }

    public function scopeManual($query)
    {
        return $query->where('manual', 1);
    }

    public function currencies()
    {
        return $this->hasMany(PaymentGatewayCurrency::class, 'payment_gateway_id');
    }

    public function defaultCurrency()
    {
        return $this->hasOne(PaymentGatewayCurrency::class, 'payment_gateway_id')
            ->where('is_default', true);
    }

    public function getImageUrlAttribute()
    {
        
        return get_img(filePath('paymentGateway') . '/' . $this->image);   
    }
}
