<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicketReply extends Model
{
    protected $fillable = [
        'ticket_id', 'admin_id', 'user_id', 'is_admin', 'message', 'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_admin' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    // helpers
    public function replierName(): string
    {
        if ($this->is_admin && function_exists('admin') && admin() && $this->admin_id) {
            return optional(\App\Models\Admin::find($this->admin_id))->name ?? __('Admin');
        }
        if (!$this->is_admin && $this->user_id) {
            return optional(\App\Models\User::find($this->user_id))->name ?? __('User');
        }
        return $this->is_admin ? __('Admin') : __('User');
    }
}
