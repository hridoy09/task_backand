<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    public function verify(string $token, string $ip = null, string $type = 'v2_checkbox', float $minScore = 0.5): bool
    {
        $secret = data_get(integration('recaptcha'), 'settings.secret_key');
        if (! $secret) return false;

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => $ip,
        ])->json();

        if (! $response || empty($response['success'])) {
            return false;
        }

        if ($type === 'v3_score') {
            $score = (float) ($response['score'] ?? 0);
            return $score >= $minScore;
        }

        // v2 checkbox
        return true;
    }
}
