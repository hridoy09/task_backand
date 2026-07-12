<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function list()
    {
        goIfUserCan('view-integrations'); 

        $title = __('Integrations');
        $integrations = Integration::orderBy('name')->paginate(20);

        return view('admin.setting.integration.list', compact('title', 'integrations'));
    }

    public function create()
    {
        goIfUserCan('save-integrations');

        $title = __('Add Integration');
        return view('admin.setting.integration.form', compact('title'));
    }

    public function edit($id)
    {
        goIfUserCan('save-integrations');

        $title = __('Edit Integration');
        $integration = Integration::findOrFail($id);

        return view('admin.setting.integration.form', compact('title', 'integration'));
    }

    /**
     * Create or update (id is optional; if provided, updates).
     */
    public function store(Request $request, $id = null)
    {
        goIfUserCan('save-integrations');

        $rules = [
            'name'    => 'required|string|max:190',
            'key'     => 'required|string|max:120|unique:integrations,key' . ($id ? ',' . $id : ''),
            'enabled' => 'sometimes|boolean',
            // If your form posts nested settings[]:
            'settings' => 'nullable|array',
        ];

        // When updating, allow the same key
        if ($id) {
            $rules['key'] = 'required|string|max:120|unique:integrations,key,' . $id;
        }

        $request->validate($rules);

        $integration = $id ? Integration::findOrFail($id) : new Integration();

        $integration->name    = $request->name;
        $integration->key     = $request->key;
        $integration->enabled = (bool) $request->boolean('enabled');

        // Accept settings either from settings[] or infer from all extra fields
        $settings = $request->input('settings', []);
        if (empty($settings)) {
            $settings = collect($request->except([
                '_token',
                '_method',
                'name',
                'key',
                'enabled'
            ]))->toArray();
        }
        $integration->settings = $settings;

        $integration->save();

        return to_route('admin.setting.integration.list')->withSuccess(__('Integration saved successfully'));
    }

    public function update(Request $request, $id)
    {
        return $this->store($request, $id);
    }

    public function toggle($id)
    {
        goIfUserCan('save-integrations');

        $integration = Integration::findOrFail($id);
        $integration->enabled = ! $integration->enabled;
        $integration->save();

        return back()->withSuccess(__('Integration status updated'));
    }

    public function delete($id)
    {
        goIfUserCan('integrations-delete');

        $integration = Integration::findOrFail($id);
        $integration->delete();

        return to_route('admin.setting.integration.list')->withSuccess(__('Integration deleted successfully'));
    }
}
