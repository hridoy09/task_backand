<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class MailTemplate extends Model
{
    protected $fillable = [
        'name',
        'code',
        'subject',
        'body',
        'shortcodes',
        'attachments',
        'view',
    ];

    protected $casts = [
        'shortcodes' => 'array',
        'attachments' => 'array',
    ];

    public function getShortcodeCollectionAttribute(): Collection
    {
        return collect($this->shortcodes ?? [])
            ->map(function ($row) {
                return [
                    'key' => $row['key'] ?? null,
                    'description' => $row['description'] ?? null,
                ];
            })
            ->filter(fn ($row) => filled($row['key']));
    }

    public function getAttachmentCollectionAttribute(): Collection
    {
        return collect($this->attachments ?? [])
            ->map(function ($row) {
                if (is_array($row)) {
                    return [
                        'path' => $row['path'] ?? null,
                        'name' => $row['name'] ?? basename($row['path'] ?? ''),
                    ];
                }

                return [
                    'path' => $row,
                    'name' => basename($row),
                ];
            })
            ->filter(fn ($row) => filled($row['path']));
    }
}
