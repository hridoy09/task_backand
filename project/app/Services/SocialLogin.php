<?php

namespace App\Services;

use App\Models\SocialLoginConfig;

class SocialLogin
{
    protected array $providers = [];

    private $config = null;

    public function __construct()
    {
        $this->config = literal(...SocialLoginConfig::get()->keyBy('key'));

        $this->providers = [
            'google' => [
                'name'   => 'Google',
                'image'  => 'images/social-login/google.png',
                'status' => $this->config->google->status ?? false,
                'icon'   => 'fab fa-google',
                'fields' => [
                    ['name' => 'client_id', 'label' => 'Client ID', 'type' => 'text', 'value' => $this->value('google', 'client_id')],
                    ['name' => 'client_secret', 'label' => 'Client Secret', 'type' => 'text', 'value' => $this->value('google', 'client_secret')],
                    ['name' => 'redirect', 'label' => 'Redirect URI', 'type' => 'text', 'value' => $this->value('google', 'redirect')],
                ],
            ],

            'facebook' => [
                'name'   => 'Facebook',
                'image'  => 'images/social-login/facebook.png',
                'status' => $this->config->facebook->status ?? false,
                'icon'   => 'fab fa-facebook-f',
                'fields' => [
                    ['name' => 'client_id', 'label' => 'App ID', 'type' => 'text', 'value' => $this->value('facebook', 'client_id')],
                    ['name' => 'client_secret', 'label' => 'App Secret', 'type' => 'text', 'value' => $this->value('facebook', 'client_secret')],
                    ['name' => 'redirect', 'label' => 'Redirect URI', 'type' => 'text', 'value' => $this->value('facebook', 'redirect')],
                ],
            ],

            'github' => [
                'name'   => 'GitHub',
                'image'  => 'images/social-login/github.png',
                'status' => $this->config->github->status ?? false,
                'icon'   => 'fab fa-github',
                'fields' => [
                    ['name' => 'client_id', 'label' => 'Client ID', 'type' => 'text', 'value' => $this->value('github', 'client_id')],
                    ['name' => 'client_secret', 'label' => 'Client Secret', 'type' => 'text', 'value' => $this->value('github', 'client_secret')],
                    ['name' => 'redirect', 'label' => 'Redirect URI', 'type' => 'text', 'value' => $this->value('github', 'redirect')],
                ],
            ],

            'linkedin' => [
                'name'   => 'LinkedIn',
                'image'  => 'images/social-login/linkedin.png',
                'status' => $this->config->linkedin->status ?? false,
                'icon'   => 'fab fa-linkedin-in',
                'fields' => [
                    ['name' => 'client_id', 'label' => 'Client ID', 'type' => 'text', 'value' => $this->value('linkedin', 'client_id')],
                    ['name' => 'client_secret', 'label' => 'Client Secret', 'type' => 'text', 'value' => $this->value('linkedin', 'client_secret')],
                    ['name' => 'redirect', 'label' => 'Redirect URI', 'type' => 'text', 'value' => $this->value('linkedin', 'redirect')],
                ],
            ],

            'twitter' => [
                'status' => $this->config->twitter->status ?? false,
                'name'   => 'Twitter (X)',
                'image'  => 'images/social-login/twitter.png',
                'icon'   => 'fab fa-twitter',
                'fields' => [
                    ['name' => 'client_id', 'label' => 'Client ID', 'type' => 'text', 'value' => $this->value('twitter', 'client_id')],
                    ['name' => 'client_secret', 'label' => 'Client Secret', 'type' => 'text', 'value' => $this->value('twitter', 'client_secret')],
                    ['name' => 'redirect', 'label' => 'Redirect URI', 'type' => 'text', 'value' => $this->value('twitter', 'redirect')],
                ],
            ],

            'bitbucket' => [
                'name'   => 'Bitbucket',
                'image'  => 'images/social-login/bitbucket.png',
                'status' => $this->config->bitbucket->status ?? false,
                'icon'   => 'fab fa-bitbucket',
                'fields' => [
                    ['name' => 'client_id', 'label' => 'Client ID', 'type' => 'text', 'value' => $this->value('bitbucket', 'client_id')],
                    ['name' => 'client_secret', 'label' => 'Client Secret', 'type' => 'text', 'value' => $this->value('bitbucket', 'client_secret')],
                    ['name' => 'redirect', 'label' => 'Redirect URI', 'type' => 'text', 'value' => $this->value('bitbucket', 'redirect')],
                ],
            ],

            'gitlab' => [
                'name'   => 'GitLab',
                'image'  => 'images/social-login/gitlab.png',
                'status' => $this->config->gitlab->status ?? false,
                'icon'   => 'fab fa-gitlab',
                'fields' => [
                    ['name' => 'client_id', 'label' => 'Client ID', 'type' => 'text', 'value' => $this->value('gitlab', 'client_id')],
                    ['name' => 'client_secret', 'label' => 'Client Secret', 'type' => 'text', 'value' => $this->value('gitlab', 'client_secret')],
                    ['name' => 'redirect', 'label' => 'Redirect URI', 'type' => 'text', 'value' => $this->value('gitlab', 'redirect')],
                ],
            ],

            'twitch' => [
                'name'   => 'Twitch',
                'image'  => 'images/social-login/twitch.png',
                'icon'   => 'fab fa-twitch',
                'status' => $this->config->twitch->status ?? false,
                'fields' => [
                    ['name' => 'client_id', 'label' => 'Client ID', 'type' => 'text', 'value' => $this->value('twitch', 'client_id')],
                    ['name' => 'client_secret', 'label' => 'Client Secret', 'type' => 'text', 'value' => $this->value('twitch', 'client_secret')],
                    ['name' => 'redirect', 'label' => 'Redirect URI', 'type' => 'text', 'value' => $this->value('twitch', 'redirect')],
                ],
            ],

            'spotify' => [
                'name'   => 'Spotify',
                'image'  => 'images/social-login/spotify.png',
                'status' => $this->config->spotify->status ?? false,
                'icon'   => 'fab fa-spotify',
                'fields' => [
                    ['name' => 'client_id', 'label' => 'Client ID', 'type' => 'text', 'value' => $this->value('spotify', 'client_id')],
                    ['name' => 'client_secret', 'label' => 'Client Secret', 'type' => 'text', 'value' => $this->value('spotify', 'client_secret')],
                    ['name' => 'redirect', 'label' => 'Redirect URI', 'type' => 'text', 'value' => $this->value('spotify', 'redirect')],
                ],
            ],
        ];
    }


    private function value($providerKey, $configKey)
    {
        return $this->config->$providerKey->config[$configKey] ?? '';
    }

    public function all(): array
    {
        return $this->providers;
    }

    public function renderFields($key)
    {
        $fields = $this->get($key)['fields'] ?? [];
        return view('admin.setting.social_login.render_fields', compact('fields', 'key'))->render();
    }

    public function saveConfig() {
        
    }

    public function get(string $provider): mixed
    {
        return $this->providers[$provider] ?? null;
    }

    public function fields(string $provider): array
    {
        return $this->providers[$provider]['fields'] ?? [];
    }

    public function enabled(): array
    {
        return array_filter($this->providers, function ($provider) {
            return $provider['status'] && collect($provider['fields'])->every(fn($f) => !empty($f['value']));
        });
    }
}
