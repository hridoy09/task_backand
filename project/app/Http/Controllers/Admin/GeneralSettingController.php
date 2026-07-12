<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Rules\SafeUploadedFile;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Facades\System;
use App\Services\FileManager;
use DateTimeZone;

class GeneralSettingController extends Controller
{
    public function generalSetting()
    {
        goIfUserCan('view-settings.general-settings');

        $title = 'General Setting';

        $generalSetting = generalSetting();

        return view('admin.setting.general', compact('title', 'generalSetting'));
    }

    public function updateGeneralSetting(Request $request, FileService $fileService)
    {
        goIfUserCan('save-settings.general-settings');
        
   
        try {

            $validated = $request->validate([
                'site_title'       => 'required|string|max:255',
                'site_description' => 'nullable|string|max:1000',
                'site_email'       => 'nullable|email|max:255',
                'site_phone'       => 'nullable|string|max:50',
                'app_url'          => 'url|required|string|max:255',
                'site_logo_dark'   => ['nullable', new SafeUploadedFile(allowedExtensions: ['png', 'jpeg', 'jpg'], maxBytes: 2024 * 1024 * 1024)],
                'site_logo'        => ['nullable', new SafeUploadedFile(allowedExtensions: ['png', 'jpeg', 'jpg'], maxBytes: 2024 * 1024 * 1024)],
                'site_favicon'     => ['nullable', new SafeUploadedFile(allowedExtensions: ['png', 'jpeg', 'jpg'], maxBytes: 2024 * 1024 * 1024)],
                'currency'         => 'required|in:' . collect(System::currencies())->pluck('code')->implode(','),
                'timezone'         => 'required|in:' . implode(',', DateTimeZone::listIdentifiers()),
                'admin_prefix'     => 'nullable',
                'user_prefix'      => 'nullable',
                'app_env'          => 'required|in:local,production',
                'demo_mode'        => 'required|in:0,1'
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $e->errors(),
                    'message' => 'Validation failed',
                    'success' => false,
                ], 422);
            }
            throw $e;
        }

        $setting = \App\Models\GeneralSetting::first();

        if (!$setting) {
            $setting = new \App\Models\GeneralSetting();
        }

        foreach ($validated as $key => $value) {
            $setting->$key = $value;
        }

        if ($request->hasFile('site_logo')) {
            $setting->site_logo = FileManager::uploadToAssets(
                $request->file('site_logo'),
                '/assets/images/logo-icon',
                generalSetting('site_logo')
            );
         
        }

        if ($request->hasFile('site_logo_dark')) {
            $setting->site_logo_dark = FileManager::uploadToAssets(
                $request->file('site_logo_dark'),
                '/assets/images/logo-icon',
                generalSetting('site_logo_dark')
            );
        
        }

        if ($request->hasFile('site_favicon')) {+
            $setting->site_favicon = FileManager::uploadToAssets(
                $request->file('site_favicon'),
                '/assets/images/logo-icon',
                generalSetting('site_favicon')
            );
        }

        $setting->save();

        System::clearCache();

        if ($request->ajax()) {
            return response()->json([
                'message' => __('Settings saved successfully'),
                'success' => true
            ]);
        }

        return back()->withSuccess(__('General settings updated successfully.'));
    }


}
