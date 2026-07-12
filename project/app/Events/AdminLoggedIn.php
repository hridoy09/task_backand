<?php

namespace App\Events;

use App\Models\Admin;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminLoggedIn
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Admin $admin,
        public string $username,
        public string $ip,
        public string $device,
        public string $browser,
        public string $os,
        public string $sessionId
    ) {}
}
