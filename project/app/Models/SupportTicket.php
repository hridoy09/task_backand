<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use Modeling;
    
    public const OPEN     = 1;
    public const ANSWERED = 2;
    public const PENDING  = 3;
    public const CLOSED   = 0;
    
    protected $fillable = [
        'department_id',
        'title',
        'slug',
        'body',
        'status',           // 0=closed,1=open,2=answered,3=pending
        'priority',         // 0=low,1=normal,2=high,3=urgent
        'added_by',
        'last_replied_by',
        'last_replied_at',
        'meta',
        'user_id',
    ];

    protected $casts = [
        'meta'            => 'array',
        'last_replied_at' => 'datetime',
        'status'          => 'integer',
        'priority'        => 'integer',
        'attachments'     => 'array'
    ];

    /* -----------------------
       Relationships
    ----------------------- */
    public function department()
    {
        return $this->belongsTo(SupportDepartment::class, 'department_id');
    }

    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')->latest();
    }

    /* -----------------------
       Scopes
    ----------------------- */
    public function scopeOpen($q)     { return $q->where('status', 1); } // open
    public function scopeAnswered($q) { return $q->where('status', 2); } // answered
    public function scopePending($q)  { return $q->where('status', 3); } // pending
    public function scopeClosed($q)   { return $q->where('status', 0); } // closed

    public function scopePriority($q, $level)
    {
        return $q->where('priority', (int) $level);
    }

    public function getStatusBadgeAttribute()
    {
        $map = [
            1 => ['Open',     'badge-info'],
            2 => ['Answered', 'badge-success'],
            3 => ['Pending',  'badge-warning'],
            0 => ['Closed',   'badge-secondary'],
        ];
        [$text, $cls] = $map[$this->status] ?? ['Unknown', 'badge-secondary'];

        return '<span class="badge '.$cls.'">'.__($text).'</span>';
    }

    public function getPriorityBadgeAttribute()
    {
        $map = [
            0 => ['Low',    'badge-secondary'],
            1 => ['Normal', 'badge-primary'],
            2 => ['High',   'badge-warning'],
            3 => ['Urgent', 'badge-danger'],
        ];
        [$text, $cls] = $map[$this->priority] ?? ['Normal', 'badge-primary'];

        return '<span class="badge '.$cls.'">'.__($text).'</span>';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
