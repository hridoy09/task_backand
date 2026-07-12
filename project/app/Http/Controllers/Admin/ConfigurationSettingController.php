<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Facades\System;
use Illuminate\Validation\ValidationException;

class ConfigurationSettingController extends Controller
{
    public function configurationSetting()
    {
        goIfUserCan('view-settings.system-configuration');

        $title = 'Configuration Settting';

        $generalSetting = generalSetting();

        return view('admin.setting.configuration', compact('title', 'generalSetting'));
    }

    public function updateConfigurationSetting(Request $request)
    {
        goIfUserCan('save-settings.system-configuration');

        try {
            $request->validate([
                'user_registration'        => 'sometimes',
                'kyc'                      => 'sometimes',
                'maintenance_mode'         => 'sometimes',
                'force_ssl'                => 'sometimes',
                'user_api'                 => 'sometimes',
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors'  => $e->errors(),
                    'success' => false
                ], 422);
            }

            throw $e;
        }

        $generalSetting                           = generalSetting();
        $generalSetting->kyc                      = $request->boolean('kyc');
        $generalSetting->user_registration        = $request->boolean('user_registration');
        $generalSetting->maintenance_mode         = $request->boolean('maintenance_mode');
        $generalSetting->force_ssl                = $request->boolean('force_ssl');
        $generalSetting->user_api                 = $request->boolean('user_api');
        $generalSetting->save();

        System::clearCache();

        if ($request->ajax()) {
            return response()->json([
                'message' => __('Configuration saved successfully'),
                'success' => true
            ]);
        }

        return back()->withSuccess(__('Confiugration saved successfully'));
    }
}
