<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Cookie;

class TwoFactorController extends Controller
{
    /** Settings page (enable/disable UI) */
    public function show(Request $request)
    {
        $user = $request->user();

        // If a temp secret exists in session (while confirming)
        $pendingSecret = session('2fa:pending_secret');

        $qrSvg = null;
        if ($pendingSecret) {
            $qrSvg = $this->makeQrSvg($this->makeOtpAuthUrl($user->email, $pendingSecret));
        }

        $recovery = [];
        if ($user->two_factor_recovery_codes) {
            $recovery = json_decode($user->two_factor_recovery_codes, true) ?: [];
        }

        return theme('user.auth.2fa', compact('user', 'pendingSecret', 'qrSvg', 'recovery'));
    }

    /** Start enabling: generate secret, show QR, ask for one code to confirm */
    public function start(Request $request)
    {
        $request->validate(['confirm_password' => 'required|current_password']);
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey(32);

        session([
            '2fa:pending_secret' => $secret,
        ]);

        return back()->with('status', 'Scan the QR and enter a code to confirm.');
    }

    /** Confirm enabling by verifying a code from the authenticator */
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required']);
        $user = $request->user();
        $secret = session('2fa:pending_secret');
        abort_unless($secret, 400, 'No pending 2FA setup.');

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($secret, preg_replace('/\s+/', '', $request->code), 1);
        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid code. Try again.']);
        }

        // Persist secret & generate recovery codes
        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_confirmed_at = now();
        $user->two_factor_recovery_codes = json_encode($this->newRecoveryCodes());
        $user->save();

        session()->forget('2fa:pending_secret');

        return back()->with('status', 'Two-factor enabled!');
    }

    /** Disable 2FA */
    public function disable(Request $request)
    {
        $request->validate(['confirm_password' => 'required|current_password']);
        $u = $request->user();
        $u->two_factor_secret = null;
        $u->two_factor_recovery_codes = null;
        $u->two_factor_confirmed_at = null;
        $u->save();

        return back()->with('status', 'Two-factor disabled.');
    }

    /** Regenerate recovery codes */
    public function regenerateCodes(Request $request)
    {
        $request->validate(['confirm_password' => 'required|current_password']);
        $u = $request->user();
        abort_unless($u->two_factor_secret, 400);

        $u->two_factor_recovery_codes = json_encode($this->newRecoveryCodes());
        $u->save();

        return back()->with('status', 'Recovery codes regenerated.');
    }

    /** Challenge screen (after password) */
    public function challenge(Request $request)
    {
        abort_unless(session('2fa:user:id'), 403);
        return theme('user.auth.two-factor-challenge');
    }

    /** Verify TOTP or recovery code; finalize login */
    public function verify(Request $request)
    {
        $request->validate([
            'code'            => 'nullable|string',
            'recovery_code'   => 'nullable|string',
            'remember_device' => 'nullable|boolean',
        ]);

        $userId = session('2fa:user:id');
        abort_unless($userId, 403);

        $userModel = config('auth.providers.users.model');
        $user      = (new $userModel)::findOrFail($userId);

        $ok = false;
        if ($request->filled('code') && $user->two_factor_secret) {
            $google2fa = new Google2FA();
            $secret = decrypt($user->two_factor_secret);
            $ok = $google2fa->verifyKey($secret, preg_replace('/\s+/', '', $request->code), 1);
        }

        // Or recovery
        if (!$ok && $request->filled('recovery_code') && $user->two_factor_recovery_codes) {
            $codes = json_decode($user->two_factor_recovery_codes, true) ?: [];
            $idx = array_search(trim($request->recovery_code), $codes, true);
            if ($idx !== false) {
                $ok = true;
                // Burn used code
                unset($codes[$idx]);
                $user->two_factor_recovery_codes = json_encode(array_values($codes));
                $user->save();
            }
        }

        if (!$ok) {
            return back()->withErrors(['code' => 'Code invalid.']);
        }

        Auth::loginUsingId($user->id, session('2fa:remember', false));

        if ($request->boolean('remember_device') && $user->two_factor_secret) {
            $secret = decrypt($user->two_factor_secret);

            // token is tied to user + current 2FA secret
            $token = hash('sha256', $user->id . '|' . $secret);

            // 30 days in minutes
            $minutes = 60 * 24 * 30;

            // local dev usually runs on http://localhost, so secure must be false there.
            $secure = app()->environment('production'); // true only in prod (https)
            $sameSite = 'Lax';                           // good default for login flows
            $path = '/';

            $domain = null; // e.g. '.myapp.com' if needed

            Cookie::queue(
                Cookie::make(
                    'remember_2fa',
                    $token,
                    $minutes,
                    $path,
                    $domain,
                    $secure,
                    true,   // httpOnly
                    false,  // raw
                    $sameSite
                )
            );
        }

        session()->forget(['2fa:user:id', '2fa:remember']);

        return redirect()->intended(route('user.dashboard'));
    }

    /* ---------------- helpers ---------------- */
    protected function makeOtpAuthUrl(string $email, string $secret): string
    {
        $issuer = rawurlencode(config('app.name', 'LaravelApp'));
        $label  = rawurlencode($email);
        return "otpauth://totp/{$issuer}:{$label}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }

    protected function makeQrSvg(string $otpauthUrl): string
    {
        $renderer = new ImageRenderer(new RendererStyle(280), new \BaconQrCode\Renderer\Image\SvgImageBackEnd());
        $writer = new Writer($renderer);
        return $writer->writeString($otpauthUrl);
    }

    protected function newRecoveryCodes(): array
    {
        return collect(range(1, 8))->map(fn() => Str::upper(Str::random(10)))->all();
    }
}
