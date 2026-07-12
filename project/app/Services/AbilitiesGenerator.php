<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Silber\Bouncer\Database\Ability;

class AbilitiesGenerator
{
    private const DEFAULT_ACTIONS = ['view', 'save', 'delete', 'manage'];

    public static function generate(): void
    {
        $abilities = collect(self::generateAbilities(self::blueprint(), self::DEFAULT_ACTIONS))
            ->merge(self::abilitiesReferencedInCode())
            ->unique('name')
            ->values();

        self::sync($abilities);
    }

    public static function generateAbilities(array $models, array $actions): array
    {
        $abilities = [];

        foreach ($models as $key => $definition) {
            if (is_string($definition)) {
                $model        = $definition;
                $title        = self::makeTitle($definition);
                $modelActions = $actions;
                $overrides    = [];
                $titles       = [];
                $extras       = [];
            } elseif (is_array($definition)) {
                $model = $definition['name'] ?? (is_string($key) ? $key : null);

                if (! $model) {
                    continue;
                }

                $title        = $definition['title'] ?? self::makeTitle($model);
                $modelActions = $definition['actions'] ?? $actions;
                $overrides    = $definition['overrides'] ?? [];
                $titles       = $definition['titles'] ?? [];
                $extras       = $definition['abilities'] ?? [];
            } else {
                continue;
            }

            foreach ($modelActions as $actionKey => $actionValue) {
                if (is_int($actionKey)) {
                    $action = $actionValue;
                    $actionLabel = $titles[$action] ?? Str::headline($action);
                } else {
                    $action = $actionKey;
                    $actionLabel = $titles[$action] ?? $actionValue;
                }

                $abilityName = strtolower($overrides[$action] ?? $action . '-' . $model);
                $abilityTitle = $titles[$action] ?? trim($actionLabel . ' ' . $title);

                $abilities[] = [
                    'name' => $abilityName,
                    'title' => $abilityTitle,
                ];
            }

            foreach ($extras as $extraKey => $extraValue) {
                if (is_int($extraKey)) {
                    $extraName = strtolower($extraValue);
                    $extraTitle = self::makeTitle($extraValue);
                } else {
                    $extraName = strtolower($extraKey);
                    $extraTitle = $extraValue ?: self::makeTitle($extraKey);
                }

                $abilities[] = [
                    'name' => $extraName,
                    'title' => $extraTitle,
                ];
            }
        }

        return $abilities;
    }

    private static function abilitiesReferencedInCode(): Collection
    {
        $patterns = [
            "/goIfUserCan\\(\\s*['\"]([^'\"\\)]+)['\"]/U",
            "/userCan\\(\\s*['\"]([^'\"\\)]+)['\"]/U",
            "/@can\\(\\s*['\"]([^'\"\\)]+)['\"]/U",
        ];

        $directories = [
            app_path('Http/Controllers/Admin'),
            resource_path('views/admin'),
        ];

        $abilities = collect();

        $propertyPatterns = [
            '/protected\\s+(?:\\?string\\s+)?\\$viewPermission\\s*=\\s*[\'\"]([^\'\"]+)[\'\"]/i',
            '/protected\\s+(?:\\?string\\s+)?\\$createPermission\\s*=\\s*[\'\"]([^\'\"]+)[\'\"]/i',
            '/protected\\s+(?:\\?string\\s+)?\\$updatePermission\\s*=\\s*[\'\"]([^\'\"]+)[\'\"]/i',
            '/protected\\s+(?:\\?string\\s+)?\\$deletePermission\\s*=\\s*[\'\"]([^\'\"]+)[\'\"]/i',
        ];

        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                continue;
            }

            foreach (File::allFiles($directory) as $file) {
                $contents = $file->getContents();

                self::extractAbilitiesFromContent($abilities, $contents, $patterns);
                self::extractAbilitiesFromContent($abilities, $contents, $propertyPatterns);
            }
        }

        return $abilities;
    }

    private static function extractAbilitiesFromContent(Collection $abilities, string $contents, array $patterns): void
    {
        foreach ($patterns as $pattern) {
            if (! preg_match_all($pattern, $contents, $matches)) {
                continue;
            }

            foreach ($matches[1] as $match) {
                $match = trim($match);

                if ($match === '') {
                    continue;
                }

                $abilities->push([
                    'name' => strtolower($match),
                    'title' => self::makeTitle($match),
                ]);
            }
        }
    }

    private static function sync(Collection $abilities): void
    {
        $names = $abilities->pluck('name')->all();

        $abilities->each(function (array $ability) {
            $model = Ability::firstOrNew(['name' => $ability['name']]);
            $model->title = $ability['title'];

            $options = $model->options ?? [];
            $options['system_generated'] = true;
            $model->options = $options;

            $model->save();
        });

        if (! empty($names)) {
            Ability::query()
                ->whereNotIn('name', $names)
                ->whereNull('entity_type')
                ->whereJsonContains('options->system_generated', true)
                ->delete();
        }
    }

    private static function makeTitle(string $value): string
    {
        $value = str_replace(['.', '_'], ' ', $value);

        return Str::headline(str_replace('-', ' ', $value));
    }

    private static function blueprint(): array
    {
        return [];
    }
}
