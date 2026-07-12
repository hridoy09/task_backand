<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLoginConfig;
use App\Services\SocialLogin;
use Illuminate\Http\Request;

class SocialLoginConfigController extends Controller
{
    public function changeStatus(Request $request, $key)
    {
        goIfUserCan('save-social-login-providers');

        $socialLoginConfig = SocialLoginConfig::where('key', $key)->first();

        if(!$socialLoginConfig) {
            $socialLoginConfig         = new SocialLoginConfig();
            $socialLoginConfig->config = [];
            $socialLoginConfig->key    = $key;
            $socialLoginConfig->save();
        }

        $socialLoginConfig->status = $request->boolean('status');
        $socialLoginConfig->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Providre status changed')
        ]);
    }
    
    public function saveConfig(Request $request, $key)
    {
        goIfUserCan('save-social-login-providers');

        $config = $request->except('_token');

        $socialLogin = SocialLoginConfig::where('key', $key)->first();

        if(!$socialLogin) $socialLogin = new SocialLoginConfig();
        $socialLogin->config = $config;
        $socialLogin->key = $key;
        $socialLogin->save();

        return back()->withSuccess(__('Configuration saved successfully'));
    }
    
    public function list(SocialLogin $socialLogin)
    {
        goIfUserCan('view-social-login-providers');

        $title = 'Social Login Providers';

        $providers = $socialLogin->all();

        return view('admin.setting.social_login.list', compact('title', 'providers'));
    }

    public function getFields($key, SocialLogin $socialLogin)
    {
        goIfUserCan('view-social-login-providers');

        return $socialLogin->renderFields($key);
    }
}
