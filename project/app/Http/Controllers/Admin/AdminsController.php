<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;

class AdminsController extends BaseCrudController
{
    protected string $model = Admin::class;

    protected string $listView = 'admin.admins.list';

    protected ?string $formView = 'admin.admins.form';

    protected ?string $viewPermission = 'view-members';

    protected ?string $createPermission = 'save-members';

    protected ?string $updatePermission = 'save-members';

    protected array $rules = [
        'name'         => 'required',
        'email'        => 'required|email|unique:admins,id,{id}',
        'username'     => 'required|unique:admins,id,{id}',
        'password'     => 'sometimes',
        'phone_number' => 'required|unique:admins,id,{id}',
    ];

    public function __construct()
    {
        viewShare($this->formView, ['roles' => Role::get()]);
    }

    public function create()
    {
        return $this->dataCreate(__('Add New Member'), 'save-members');
    }

    protected function beforeDataSave($request, $model, $id)
    {
        // $model->name = $request->name;
        // $model->username = $request->username;
        // $model->email = $request->email;
        // $model->phone_number = $request->phone_number;
        

        if ($request->filled('password')) {
            $model->password = Hash::make($request->password);
        }
    }

    protected function afterDataSave($request, $model, $id)
    {
        if ($request->role_id) {
            $role = Role::find($request->role_id);
            if ($role) {
                Bouncer::sync($model)->roles([$role->name]);
            }
        }
    }

    public function edit($id)
    {
        return $this->dataEdit(__('Edit Member'), $id, 'save-members');
    }

    public function delete($id)
    {
        goIfUserCan('members-delete');

        abort_if($id == 1, 403, __('You cannot remove this item'));

        $member = Admin::findOrFail($id);

        $member->delete();

        return to_route('admin.admin.list')->withSuccess(__('Admin Deleted Successfully'));
    }
}
