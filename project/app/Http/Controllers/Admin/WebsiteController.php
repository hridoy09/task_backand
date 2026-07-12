<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SystemHelper;
use App\Models\Page;
use App\Models\Setting;
use App\Services\FileManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\RequiredIf;

class WebsiteController extends Controller
{
    private $sectionsConfig;

    public function __construct()
    {
        $this->sectionsConfig = SystemHelper::sections();
    }

    public function pages()
    {
        goIfUserCan('view-website.pages');

        $title = 'Pages';

        $pages = Page::paginate();

        return view('admin.website.pages.list', compact('pages', 'title'));
    }

    public function deletePage($id)
    {
        goIfUserCan('delete-website.pages');

        $page = Page::findOrFail($id);

        if ($page->is_default) {
            return back()->withError(__('You cannot delete the default page'));
        }

        $page->delete();

        return back()->withSuccess(__('Page deleted successfully'));
    }

    public function editPage($pageId)
    {
        goIfUserCan('save-website.pages');

        $title = 'Edit Pages';

        $page = Page::findOrFail($pageId);

        $sections = $this->sectionsConfig;

        return view('admin.website.pages.edit', compact('page', 'title', 'sections'));
    }

    public function newPage()
    {
        goIfUserCan('save-website.pages');

        $title = 'Add New Page';

        return view('admin.website.pages.add', compact('title'));
    }

    public function saveNewPage(Request $request)
    {
        goIfUserCan('save-website.pages');

        $request->validate([
            'title' => 'required|unique:pages,title'
        ]);

        $page        = new Page();
        $page->title = $request->title;
        $page->slug  = str()->slug($request->title);
        $page->save();

        return to_route('admin.website.page.edit', $page->id)->withSuccess('Update the page section');
    }

    public function updatePage(Request $request, $pageId)
    {
        goIfUserCan('save-website.pages');

        $page = Page::findOrFail($pageId);

        $metaKeywords = [];
        $metaKeywords = collect(json_decode($request->seo_content['meta_keywords'] ?? '[]', true))
            ->pluck('value')
            ->filter()
            ->toArray();

        $rules = [
            'ordered_sections'               => 'sometimes',
            'title'                          => $page->is_default ? 'nullable' :  'required',
            'seo_content.meta_title'         => 'nullable|string|max:255',
            'seo_content.social_title'       => 'nullable|string|max:255',
            'seo_content.social_description' => 'nullable|string|max:255',
            'seo_content.image'              => 'nullable|file',
            'seo_content.meta_description'   => 'nullable|string|max:500',
            'seo_content.meta_keywords'      => 'nullable|string|max:255',
            'content'                        => [new RequiredIf(function () use ($page) {
                return $page->privacy;
            })]
        ];

        $request->validate($rules);

        $seoContent = $request->seo_content ?? [];
        $seoContent['meta_keywords'] = $metaKeywords;

        if ($request->hasFile('seo_content.image')) {
            $seoContent['image'] = FileManager::uploadToAssets(
                $request->file('seo_content.image'),
                filePath('seo'),
                $page?->seo_content['image'] ?? null
            );
        } else {
            $seoContent['image'] = $page?->seo_content['image'] ?? null;
        }

        if (!$page->is_default) {
            $page->title    = $request->title;
            $page->slug     = str()->slug($request->title);
        }

        if (!$page->privacy) {
            $page->sections = $request->ordered_sections ?? [];
            $page->seo_content = $seoContent;
        } else {
            $page->content = $request->content;
        }

        $page->save();

        return back()->withSuccess(__('Page updated successfully'));
    }

    public function sections()
    {
        goIfUserCan('view-website.sections');

        $title = 'Website Frontend Sections';

        $sections = $this->sectionsConfig;
        return view('admin.website.sections.list', compact('sections', 'title'));
    }

    public function editSection($key)
    {
        goIfUserCan('save-website.sections');

        abort_unless(array_key_exists($key, $this->sectionsConfig), 404);

        $title = 'Edit Section: ' . $this->sectionsConfig[$key]['title'];
        $sectionConfig = $this->sectionsConfig[$key]; // Configuration for the section
        $sectionKey = $key;

        $settingKey = 'section_' . $key . '_content';
        $setting = Setting::where('key', $settingKey)->first();
        $contentData = $setting ? $setting->value : []; // value is already an array due to casts

        return view('admin.website.sections.edit', compact('sectionConfig', 'sectionKey', 'title', 'contentData'));
    }

    public function updateSection(Request $request, $key)
    {
        goIfUserCan('save-website.sections');

        abort_unless(array_key_exists($key, $this->sectionsConfig), 404);

        $sectionConfig = $this->sectionsConfig[$key]['config'];
        $inputData     = $request?->content ?? [];
        $contentToSave = [];

        $uploadPath = "sections/{$key}";
        $settingKey = "section_{$key}_content";

        $existingSetting = Setting::where('key', $settingKey)->first();
        $existingContent = $existingSetting ? $existingSetting->value : [];

        foreach ($sectionConfig as $fieldKey => $field) {
            if ($field['type'] === 'group') {
                $contentToSave[$fieldKey] = [];

                foreach ($field['fields'] as $childKey => $childField) {
                    $currentValue = $existingContent[$fieldKey][$childKey] ?? null;

                    if ($childField['type'] === 'image') {
                        if ($request->hasFile("content.{$fieldKey}.{$childKey}")) {

                            $filePath = FileManager::uploadToAssets(
                                $request->file("content.{$fieldKey}.{$childKey}"),
                                filePath('sectionImage'),
                                null,
                                fileSizes('sectionImage')
                            );
                            $contentToSave[$fieldKey][$childKey] = "storage/{$filePath}";
                        } else {
                            $contentToSave[$fieldKey][$childKey] = $currentValue;
                        }
                    } else {
                        $contentToSave[$fieldKey][$childKey] = $inputData[$fieldKey][$childKey] ?? null;
                    }
                }
            }

            // Handle repeater fields
            elseif ($field['type'] === 'repeater') {
                $contentToSave[$fieldKey] = [];

                foreach ($inputData[$fieldKey] ?? [] as $index => $item) {

                    $repeaterItemData = [];

                    foreach ($field['fields'] as $subKey => $subField) {
                        $currentValue = $existingContent[$fieldKey][$index][$subKey] ?? null;

                        if ($subField['type'] === 'image') {
                            if ($request->hasFile("content.{$fieldKey}.{$index}.{$subKey}")) {

                                $filePath = FileManager::uploadToAssets(
                                    $request->file("content.{$fieldKey}.{$index}.{$subKey}"),
                                    filePath('sectionImage'),
                                    null,
                                    fileSizes('sectionImage')
                                );


                                $repeaterItemData[$subKey] = $filePath;
                            } else {
                                $repeaterItemData[$subKey] = $item[$subKey . '_existing'] ?? $currentValue ?? null;
                            }

                            unset($repeaterItemData[$subKey . '_existing']);
                        } else {
                            $repeaterItemData[$subKey] = $item[$subKey] ?? null;
                        }
                    }

                    $contentToSave[$fieldKey][] = $repeaterItemData;
                }
            }

            // Handle single image fields
            elseif ($field['type'] === 'image') {
                $currentValue = $existingContent[$fieldKey] ?? null;

                if ($request->hasFile("content.{$fieldKey}")) {
                    $filePath = FileManager::uploadToAssets(
                        $request->file("content.{$fieldKey}"),
                        filePath('sectionImage'),
                        null,
                        fileSizes('sectionImage')
                    );


                    $contentToSave[$fieldKey] = $filePath;
                } else {
                    $contentToSave[$fieldKey] = $currentValue;
                }
            }

            // Handle text, textarea, etc.
            else {
                $contentToSave[$fieldKey] = $inputData[$fieldKey] ?? null;
            }
        }

        Setting::updateOrCreate(
            ['key' => $settingKey],
            ['value' => $contentToSave]
        );

        return redirect()->route('admin.website.section.edit', $key)->with('success', 'Section updated successfully!');
    }
}
