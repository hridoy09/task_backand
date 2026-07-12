<?php

namespace App\Services;

use App\Models\MailTemplate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MailTemplateService
{
    public static function getTemplate(string $code): ?MailTemplate
    {
        $cacheKey = 'mail-template-' . $code;

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($code) {
            return MailTemplate::where('code', $code)->first();
        });
    }

    public static function forgetTemplateCache(string $code): void
    {
        Cache::forget('mail-template-' . $code);
    }

    public static function formatShortcodes(array $replacements = []): array
    {
        $formatted = [];

        foreach ($replacements as $key => $value) {
            $placeholder = self::formatPlaceholder((string) $key);
            if ($placeholder === '[]') {
                continue;
            }
            $formatted[$placeholder] = is_scalar($value) ? (string) $value : json_encode($value);
        }

        return $formatted;
    }

    public static function getAttachmentAbsolutePaths(MailTemplate $template): array
    {
        return $template->attachment_collection
            ->map(function (array $attachment) {
                $path = $attachment['path'] ?? null;
                $name = $attachment['name'] ?? null;

                if (!$path) {
                    return null;
                }

                $disk = Storage::disk('local');
                if ($disk->exists($path)) {
                    return [
                        'path' => $disk->path($path),
                        'name' => $name ?? basename($path),
                    ];
                }

                if (file_exists($path)) {
                    return [
                        'path' => $path,
                        'name' => $name ?? basename($path),
                    ];
                }

                Log::warning('Attachment not found for mail template', [
                    'path' => $path,
                ]);

                return null;
            })
            ->filter()
            ->values()
            ->all();
    }

    public static function sendUsingTemplate(
        $recipients,
        string $code,
        array $shortcodes = [],
        string $channel = 'email',
        array $viewData = []
    ): bool {
        $template = self::getTemplate($code);

        if (!$template) {
            Log::warning('Mail template not found', ['code' => $code]);
            return false;
        }

        $shortcodePairs = self::formatShortcodes($shortcodes);

        $attachments = self::getAttachmentAbsolutePaths($template);

        return sendSystemNotification(
            to: $recipients,
            subject: $template->subject,
            body: $template->body,
            channel: $channel,
            extraData: array_merge($shortcodePairs, $viewData),
            view: $template->view ?? 'global',
            attachments: $attachments
        );
    }

    public static function availableShortcodes(?MailTemplate $template = null): Collection
    {
        $global = self::mapConfiguredShortcodes(config('mail_templates.shortcodes.global', []));

        $templateSpecific = collect();

        if ($template && filled($template->code)) {
            $templateSpecific = self::mapConfiguredShortcodes(
                data_get(config('mail_templates.shortcodes.templates', []), $template->code, [])
            );
        }

        $legacy = $template?->shortcode_collection ?? collect();

        return $global
            ->merge($templateSpecific)
            ->merge($legacy)
            ->unique('key')
            ->values();
    }

    public static function defaultShortcodeReplacements(): array
    {
        $appName = config('app.name') ?? 'Application';
        $appUrl = config('app.url');

        if (!$appUrl) {
            try {
                $appUrl = url('/');
            } catch (\Throwable $e) {
                $appUrl = 'http://localhost';
            }
        }

        $supportEmail = generalSetting('mail_from_address')
            ?? config('mail.from.address')
            ?? env('MAIL_FROM_ADDRESS')
            ?? 'support@example.com';

        return [
            '[app_name]' => $appName,
            '[app_url]' => rtrim((string) $appUrl, '/'),
            '[support_email]' => $supportEmail,
            '[year]' => date('Y'),
            '[date]' => now()->format('d M Y'),
            '[time]' => now()->format('H:i A'),
        ];
    }

    public static function syncConfiguredTemplates(): void
    {
        $configuredTemplates = config('mail_templates.templates', []);

        if (empty($configuredTemplates) || !is_array($configuredTemplates)) {
            return;
        }

        foreach ($configuredTemplates as $code => $definition) {
            if (!is_string($code) || !is_array($definition)) {
                continue;
            }

            $template = MailTemplate::firstOrNew(['code' => $code]);

            $defaults = [
                'name' => $definition['name'] ?? ucwords(strtolower(str_replace('_', ' ', $code))),
                'subject' => $definition['subject'] ?? ($definition['name'] ?? $code),
                'body' => $definition['body'] ?? '',
                'view' => $definition['view'] ?? 'global',
            ];

            foreach ($defaults as $field => $value) {
                if (blank($value)) {
                    continue;
                }

                if (!$template->exists || blank($template->{$field})) {
                    $template->{$field} = $value;
                }
            }

            $configuredShortcodes = self::mapConfiguredShortcodes(
                data_get(config('mail_templates.shortcodes.templates', []), $code, [])
            )->map(fn ($row) => [
                'key' => $row['key'],
                'description' => $row['description'],
            ]);

            if ($configuredShortcodes->isNotEmpty()) {
                $existing = $template->shortcode_collection->keyBy('key');

                foreach ($configuredShortcodes as $row) {
                    if (!$existing->has($row['key'])) {
                        $existing->put($row['key'], $row);
                    }
                }

                $merged = $existing->values()->map(function ($row) {
                    return [
                        'key' => $row['key'],
                        'description' => $row['description'],
                    ];
                })->all();

                if ($template->shortcodes !== $merged) {
                    $template->shortcodes = $merged;
                }
            }

            if ($template->isDirty() || !$template->exists) {
                $template->save();
                self::forgetTemplateCache($code);
            }
        }
    }

    protected static function mapConfiguredShortcodes(array $shortcodes): Collection
    {
        return collect($shortcodes)
            ->map(function ($description, $key) {
                if (is_int($key)) {
                    if (is_array($description) && isset($description['key'])) {
                        $key = $description['key'];
                        $description = $description['description'] ?? null;
                    } elseif (is_string($description)) {
                        $key = $description;
                        $description = null;
                    } else {
                        return null;
                    }
                } elseif (is_array($description) && isset($description['description'])) {
                    $description = $description['description'];
                }

                $placeholder = self::formatPlaceholder((string) $key);

                if ($placeholder === '[]') {
                    return null;
                }

                return [
                    'key' => $placeholder,
                    'description' => is_string($description) ? $description : null,
                ];
            })
            ->filter()
            ->values();
    }

    protected static function formatPlaceholder(string $key): string
    {
        $normalized = trim(trim($key), '[]');

        if ($normalized === '') {
            return '[]';
        }

        return '[' . $normalized . ']';
    }
}
