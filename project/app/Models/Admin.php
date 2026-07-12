<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class Admin extends Authenticatable
{
    
    use Notifiable, HasRolesAndAbilities, Modeling;

    protected $fillable = [
        'username',
        'password',
        'name',
        'email',
        'image',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function sendPasswordResetNotification($token)
    {
        if (!$this->email) {
            return;
        }

        $resetUrl = route('admin.password.set', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ]);

        sendTemplatedNotification(
            $this->email,
            'ADMIN_PASSWORD_RESET_REQUEST',
            [
                'admin_name' => $this->name,
                'reset_url' => $resetUrl,
                'request_ip' => request()?->ip() ?? 'N/A',
            ]
        );
    }

    public function adminLogins()
    {
        return $this->hasMany(AdminLogin::class);
    }

    public function getRoleAttribute()
    {
        return $this->roles()->first();
    }
}
