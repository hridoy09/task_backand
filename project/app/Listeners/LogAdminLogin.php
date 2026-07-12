<?php

namespace App\Listeners;

use App\Events\AdminLoggedIn;
use App\Models\AdminLogin;
use App\Notifications\AdminLoginAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use hisorange\BrowserDetect\Parser as Browser;

class LogAdminLogin implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;            // retry a few times
    public $backoff = [5, 30, 120]; // seconds

    public function handle(AdminLoggedIn $event): void
    {
        $ip      = $event->ip;
        $device  = $event->device;
        $browser = $event->browser;
        $os      = $event->os;

        // Geo lookup (fail-safe)
        $country = 'Unknown';
        $city    = 'Unknown';
        try {
            $res = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
            $country = $res['country'] ?? $country;
            $city    = $res['city'] ?? $city;
        } catch (\Throwable $e) {
            // swallow; not critical
        }

        // Save login row
        AdminLogin::updateOrCreate(
            ['session_id' => $event->sessionId],
            [
                'admin_id'    => $event->admin->id,
                'device_type' => $device,
                'browser'     => $browser,
                'os'          => $os,
                'ip'          => $ip,
                'country'     => $country,
                'city'        => $city,
            ]
        );

        try {
            $slack = integration('slack');
            $settings = $slack?->settings;
            if (is_string($settings)) {
                $settings = json_decode($settings, true) ?: [];
            }
            $webhookUrl = data_get($settings, 'webhook_url');

            if ($webhookUrl) {
                Notification::route('slack', $webhookUrl)
                    ->notify(new AdminLoginAlert(
                        username: $event->username,
                        ip: $ip,
                        device: $device,
                        browser: $browser,
                        os: $os
                    ));
            }
        } catch (\Throwable $e) {
            \Log::warning('Slack notify failed: '.$e->getMessage());
        }
    }
}
