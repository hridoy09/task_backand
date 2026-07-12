<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailTemplate;
use App\Services\MailTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MailTemplateController extends Controller
{
    public function index()
    {
        goIfUserCan('view-mail-templates');

        $title = __('Mail Templates');
        MailTemplateService::syncConfiguredTemplates();

        $templates = MailTemplate::orderBy('name')->paginate(20);

        return view('admin.mail_template.index', compact('templates','title'));
    }

    public function create()
    {
        goIfUserCan('save-mail-templates');

        MailTemplateService::syncConfiguredTemplates();

        $template = new MailTemplate([
            'view' => 'global',
        ]);

        $availableShortcodes = MailTemplateService::availableShortcodes();
        $emailViews = $this->emailViewOptions();

        return view('admin.mail_template.form', compact('template', 'availableShortcodes', 'emailViews'));
    }

    public function store(Request $request)
    {
        goIfUserCan('save-mail-templates');

        return $this->persist($request, new MailTemplate());
    }

    public function edit(MailTemplate $mailTemplate)
    {
        goIfUserCan('save-mail-templates');

        $title = __('Edit Mail Template');

        MailTemplateService::syncConfiguredTemplates();

        $availableShortcodes = MailTemplateService::availableShortcodes($mailTemplate);
   

        return view('admin.mail_template.form', [
            'template' => $mailTemplate,
            'availableShortcodes' => $availableShortcodes,
            'title' => $title
        ]);
    }

    public function update(Request $request, MailTemplate $mailTemplate)
    {
        goIfUserCan('save-mail-templates');

        return $this->persist($request, $mailTemplate);
    }

    public function destroy(MailTemplate $mailTemplate)
    {
        goIfUserCan('delete-mail-templates');

        foreach ($mailTemplate->attachment_collection as $attachment) {
            if (!empty($attachment['path']) && Storage::disk('local')->exists($attachment['path'])) {
                Storage::disk('local')->delete($attachment['path']);
            }
        }

        $mailTemplate->delete();
        MailTemplateService::forgetTemplateCache($mailTemplate->code);

        return back()->withSuccess(__('Mail template deleted successfully.'));
    }

    protected function persist(Request $request, MailTemplate $template)
    {
        goIfUserCan('save-mail-templates');
        $templateId = $template->id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:mail_templates,code,' . $templateId,
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120',
            'remove_attachments' => 'nullable|array',
        ]);

        $template->fill([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'subject' => $validated['subject'],
            'body' => $validated['body'], 
        ]);

        $existingAttachments = $template->attachment_collection;
        $removePaths = collect($validated['remove_attachments'] ?? []);

        $filteredExisting = $existingAttachments->reject(function ($attachment) use ($removePaths) {
            $path = $attachment['path'] ?? null;
            if ($path && $removePaths->contains($path)) {
                if (Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->delete($path);
                }
                return true;
            }

            return false;
        });

        $newAttachments = collect();

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if (!$file) {
                    continue;
                }

                $storedPath = Storage::disk('local')->putFile('mail-templates', $file);

                $newAttachments->push([
                    'path' => $storedPath,
                    'name' => $file->getClientOriginalName(),
                ]);
            }
        }

        $template->attachments = $filteredExisting
            ->merge($newAttachments)
            ->values()
            ->all();

        $template->save();

        MailTemplateService::forgetTemplateCache($template->code);

        return redirect()
            ->route('admin.setting.mail_template.index')
            ->withSuccess(__('Mail template saved successfully.'));
    }


}
