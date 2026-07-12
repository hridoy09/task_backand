<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Rules\SafeUploadedFile;
use App\Services\FileManager;
use Illuminate\Http\Request;

class GlobalSeoSettingController extends Controller
{
    public function index() 
    {
        $title = __('Global SEO Setting');
        $globalSeo = generalSetting('global_seo') ?? [];
        
        return view('admin.setting.global_seo.index', compact('title', 'globalSeo'));
    }

    public function save(Request $request) 
    {
        $metaKeywords = collect(json_decode($request->meta_keywords, true))
            ->pluck('value')
            ->filter()
            ->toArray();

        $validated = $request->validate([
            'image'              => [
                'nullable', 'file',    new SafeUploadedFile(
                    ['jpg','jpeg','png'], 
                    maxBytes: 2024 * 1024 * 1024
                ),
            ],
            'meta_description'   => 'required|string',   
            'social_title'       => 'required|string',
            'social_description' => 'required|string',   
            'meta_keywords'      => 'required|string',    
            'meta_title'         => 'required|string'
        ]);

        $setting = generalSetting();

        $seoData = [
            'meta_keywords'      => $metaKeywords,
            'meta_description'   => $validated['meta_description'],
            'social_title'       => $validated['social_title'],
            'social_description' => $validated['social_description'],
            'meta_title'         => $validated['meta_title'],
            'image' => $setting->global_seo['image'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $seoData['image'] = FileManager::uploadToAssets(
                $request->file('image'),
                filePath('seo'),
                generalSetting('image')
            );
        }

        $setting->global_seo = $seoData;
        $setting->save();

        return back()->withSuccess(__('Global SEO successfully saved'));
    }
}
