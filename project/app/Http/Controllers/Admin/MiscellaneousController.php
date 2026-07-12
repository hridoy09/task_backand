<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MiscellaneousController extends Controller
{
    public function update()
    {
        goIfUserCan('view-misc.update');

        $title = __('System Update');

        $updateAvailable = false;
        $newVersion = '2.1.0';
        $currentVersion = software()->version;

        return view('admin.miscell.update', compact('title', 'updateAvailable', 'newVersion', 'currentVersion'));
    }

    public function updateSystem(Request $request)
    {
        goIfUserCan('manage-misc.update');

        return back()->withSuccess(__('Your system updated'));
    }

    public function cache()
    {
        goIfUserCan('view-misc.cache');

        $title = __('Cache');
        return view('admin.miscell.cache', compact('title'));
    }

    public function cacheClear()
    {
        goIfUserCan('manage-misc.cache');

        software()->clearCache();
        return back()->withSuccess(__('Cache cleared successfully'));
    }

    public function customCss()
    {
        goIfUserCan('view-misc.custom-css');

        $title = __('Custom Css');

        $path = public_path('assets/custom_css.css');    
        $customCss = File::exists($path) ? File::get($path) : "/* Your custom CSS code goes here */\n";

        return view('admin.miscell.custom_css', compact('title', 'customCss'));
    }

    public function customCssSave(Request $request)
    {
        goIfUserCan('save-misc.custom-css');

        $request->validate([
            'custom_css' => 'nullable|string',
        ]);

        $directoryPath = public_path('assets');
        $filePath = public_path('assets/custom_css.css');

        try {
            if (!File::isDirectory($directoryPath)) {
                File::makeDirectory($directoryPath, 0755, true, true);
            }

            // 4. Get the CSS content from the request. Default to an empty string if null.
            $cssContent = $request->input('custom_css', '');

            File::put($filePath, $cssContent);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not save the CSS file. Please check folder permissions.');
        }

        return back()->with('success', 'Custom CSS has been saved successfully!');
    }
}
