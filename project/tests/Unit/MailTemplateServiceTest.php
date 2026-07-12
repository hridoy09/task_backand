<?php

namespace Tests\Unit;

use App\Models\MailTemplate;
use App\Services\MailTemplateService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MailTemplateServiceTest extends TestCase
{
    public function test_get_attachment_absolute_paths_returns_only_existing_files(): void
    {
        Storage::fake('local');

        Storage::disk('local')->put('mail-templates/test.txt', 'test-content');

        $template = new MailTemplate([
            'attachments' => [
                ['path' => 'mail-templates/test.txt', 'name' => 'Test.txt'],
                ['path' => 'mail-templates/missing.pdf', 'name' => 'Missing.pdf'],
            ],
        ]);

        $attachments = MailTemplateService::getAttachmentAbsolutePaths($template);

        $this->assertCount(1, $attachments);
        $this->assertSame(
            Storage::disk('local')->path('mail-templates/test.txt'),
            $attachments[0]['path']
        );
        $this->assertSame('Test.txt', $attachments[0]['name']);
    }

    public function test_available_shortcodes_merges_global_and_template_specific(): void
    {
        config()->set('mail_templates.shortcodes.global', [
            '[app_name]' => 'Application name',
            '[support_email]' => 'Support email address',
        ]);

        config()->set('mail_templates.shortcodes.templates', [
            'WELCOME' => [
                '[user_name]' => 'Name of the user',
            ],
        ]);

        $template = new MailTemplate(['code' => 'WELCOME']);

        $shortcodes = MailTemplateService::availableShortcodes($template);

        $this->assertEqualsCanonicalizing(
            ['[app_name]', '[support_email]', '[user_name]'],
            $shortcodes->pluck('key')->all()
        );

        $this->assertSame(
            'Support email address',
            $shortcodes->firstWhere('key', '[support_email]')['description']
        );
    }
}

