<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    protected $fillable = [
        'key',
        'name',
        'settings',
        'enabled',
    ];

    protected $casts = [
        'settings' => 'array',
        'enabled' => 'boolean',
    ];

    /**
     * Helper to check if integration is active.
     */
    public function isEnabled(): bool
    {
        return $this->enabled === true;
    }
}
