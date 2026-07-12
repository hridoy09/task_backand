<?php

namespace App\Http\Controllers\Admin;

use App\Models\SupportDepartment;

class SupportDepartmentController extends BaseCrudController
{
    protected string $model = SupportDepartment::class;
    protected string $listView = 'admin.support_departments.list';
    protected ?string $viewPermission = 'view-support-departments';
    protected ?string $createPermission = 'save-support-departments';
    protected ?string $updatePermission = 'save-support-departments';
    protected array $rules = [
        'name' => 'required|unique:support_departments,name,{id}',
    ];

    protected function listQuery($query)
    {
        return $query->withCount('tickets')->latest();
    }

    public function status($id)
    {
        goIfUserCan('save-support-departments');

        $dep = $this->model::findOrFail($id);
        $dep->update(['status' => !$dep->status]);

        return back()->withSuccess(__('Support department status changed'));
    }
}
