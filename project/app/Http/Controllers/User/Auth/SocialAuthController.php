<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SocialLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected $socialLogin;

    public function __construct(SocialLogin $socialLogin)
    {
        $this->socialLogin = $socialLogin;
    }

    public function redirect($provider)
    {
        $config = $this->socialLogin->get($provider);

        if (!$config || !$config['status']) {
            return redirect()->route('login')->with('error', 'Invalid or disabled provider');
        }

        // Dynamically set Socialite config from DB
        config([
            "services.$provider.client_id"     => $this->getFieldValue($config['fields'], 'client_id'),
            "services.$provider.client_secret" => $this->getFieldValue($config['fields'], 'client_secret'),
            "services.$provider.redirect"      => $this->getFieldValue($config['fields'], 'redirect'),
        ]);

        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $config = $this->socialLogin->get($provider);

            if (!$config || !$config['status']) {
                return redirect()->route('login')->with('error', 'Invalid or disabled provider');
            }

            // Set credentials dynamically again for callback
            config([
                "services.$provider.client_id"     => $this->getFieldValue($config['fields'], 'client_id'),
                "services.$provider.client_secret" => $this->getFieldValue($config['fields'], 'client_secret'),
                "services.$provider.redirect"      => $this->getFieldValue($config['fields'], 'redirect'),
            ]);

            $socialUser = Socialite::driver($provider)->stateless()->user();

            $user = User::firstOrCreate(
        ['email' => $socialUser->getEmail()],
                [
                    'name'              => $socialUser->getName() ?? $socialUser->getNickname(),
                    'password'          => bcrypt(Str::random(24)),
                    'email_verified_at' => now(),
                    'provider'          => $provider,
                    'provider_id'       => $socialUser->getId(),
                ]
            );

            auth()->login($user);

            return to_route('user.dashboard');
        } catch (\Exception $e) {
            report($e); // Optional: log the error
            return redirect()->route('login')->with('error', 'Social login failed.');
        }
    }

    private function getFieldValue(array $fields, string $fieldName): ?string
    {
        foreach ($fields as $field) {
            if ($field['name'] === $fieldName) {
                return $field['value'];
            }
        }
        return null;
    }
}
