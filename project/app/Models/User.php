<?php

namespace App\Models;

use App\Traits\Modeling;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Modeling, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'city',
        'country_code',
        'zipcode',
        'name',
        'email',
        'address',
        'password',
        'first_name',
        'last_name',
        'country',
        'phone_number',
        'image_path',
        'kyc_verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'two_factor_confirmed_at' => 'datetime',
        'email_verified_at'       => 'datetime',
        'otp_sent_at'             => 'datetime',
        'password'                => 'hashed',
    ];

    protected static function booted()
    {
        static::saving(function ($user) {
            $user->name = $user->first_name . ' ' . $user->last_name;
        });
    }

    public function getHasPendingKycAttribute()
    {
        return $this->kycSubmissions()->where('status', 'pending')->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function userLogins()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status == 1) {
            return '<span class="badge text-bg-success">' . __('Active') . '</span>';
        }

        if ($this->status == 0) {
            return '<span class="badge text-bg-danger">' . __('Inactive') . '</span>';
        }
    }

    public function getEmailVerifiedBadgeAttribute()
    {
        if ($this->email_verified_at) {
            return getBadge('Yes', 'success');
        } else {
            return getBadge('No', 'danger');
        }
    }


    public function getProfileCompletedBadgeAttribute()
    {
        if ($this->pc == 1) {
            return getBadge('Yes', 'success');
        } elseif ($this->pc == 0) {
            return getBadge('No', 'danger');
        }
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function sendPasswordResetNotification($token)
    {
        if (!$this->email) {
            return;
        }

        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        sendTemplatedNotification(
            $this->email,
            'PASSWORD_RESET_REQUEST',
            [
                'user_name'  => $this->name,
                'reset_link'  => $resetUrl,
                'request_ip' => request()?->ip() ?? 'N/A',
            ]
        );
    }

    public function sendEmailVerificationNotification()
    {
        if (!$this->email) {
            return;
        }

        $expiryMinutes = (int) (config('auth.verification.expire', 60) ?: 60);
        $expiresAt = now()->addMinutes($expiryMinutes);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            $expiresAt,
            ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())]
        );

        sendTemplatedNotification(
            $this->email,
            'EMAIL_VERIFICATION',
            [
                'user_name' => $this->name ?? '',
                'verification_url' => $verificationUrl,
                'expires_at' => $expiresAt->toDayDateTimeString(),
            ],
        );
    }

    public function scopeEmailUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    public function scopePhoneNumberUnverified($query)
    {
        return $query->whereNull('phone_verified_at');
    }


    public function scopeNew($query)
    {
        return $query->where('created_at', 'like', date("Y-m-d") . "%");
    }

    public function scopeIncompleteProfile($query)
    {
        return $query->where('pc', 0);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function kycSubmissions()
    {
        return $this->hasMany(KycSubmission::class);
    }

    public function scopeKycPending($query)
    {
        return $query->whereHas('kycSubmissions', function ($kycQuery) {
            $kycQuery->where('status', 'pending');
        });
    }
}
