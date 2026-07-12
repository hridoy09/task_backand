<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class AdminLoginAlert extends Notification
{
    use Queueable;

    public function __construct(
        public string $username,
        public string $ip,
        public string $device,
        public string $browser,
        public string $os
    ) {}

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->from('Laravel Bot', ':rocket:')
            ->content('✅ Admin login detected!')
            ->attachment(function ($attachment) {
                $attachment->title('Login details')->fields([
                    'Username' => $this->username,
                    'IP'       => $this->ip,
                    'Device'   => $this->device,
                    'Browser'  => $this->browser,
                    'OS'       => $this->os,
                    'Time'     => now()->toDateTimeString(),
                ]);
            });
    }
}
