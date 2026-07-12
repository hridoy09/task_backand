<?php

namespace App\Traits;

trait Controlling
{
    public function data(
        string $title = 'Users', 
        mixed $scope = null, 
        ?string $permission = null
    ) {
        $permission = $permission
            ?? (property_exists($this, 'viewPermission') ? $this->viewPermission : null);

        if ($permission) {
            goIfUserCan($permission);
        }

       
        $query = $this->model::query()
            ->orderBy('id','desc')
            ->searching($this->searching ?? []);

        if ($scope) {
            if(is_callable($scope)) {
                $scope($query);
            } else {
                $query->$scope();
            }
        }

        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        

        $hookMethod = $caller . 'Query'; // e.g., 'dataQuery', 'dataArchivedQuery'

        if (method_exists($this, $hookMethod)) {
            $query = $this->$hookMethod($query);
        }

        $data = $query->paginate();

        return view($this->listView, compact('title', 'data'));
    }

    public function dataCreate(
        string $title,
        ?string $permission = null
    ) {
        $permission = $permission
            ?? (property_exists($this, 'createPermission') ? $this->createPermission : null)
            ?? (property_exists($this, 'viewPermission') ? $this->viewPermission : null);

        if ($permission) {
            goIfUserCan($permission);
        }

        return view($this->formView, compact('title'));
    }

    public function dataEdit(
        string $title,
        string|int $id,
        ?string $permission = null
    ) {
        $permission = $permission
            ?? (property_exists($this, 'updatePermission') ? $this->updatePermission : null)
            ?? (property_exists($this, 'createPermission') ? $this->createPermission : null)
            ?? (property_exists($this, 'viewPermission') ? $this->viewPermission : null);

        if ($permission) {
            goIfUserCan($permission);
        }

        $model = $this->model::findOrFail($id);

        return view($this->formView, compact('title', 'model'));
    }

    public function dataSave(
        string|int|null $id = null,
        array $rules = [],
        ?string $permission = null
    ) {
        $permission = $permission ?? (
            $id
                ? ((property_exists($this, 'updatePermission') ? $this->updatePermission : null)
                    ?? (property_exists($this, 'createPermission') ? $this->createPermission : null)
                    ?? (property_exists($this, 'viewPermission') ? $this->viewPermission : null))
                : ((property_exists($this, 'createPermission') ? $this->createPermission : null)
                    ?? (property_exists($this, 'updatePermission') ? $this->updatePermission : null)
                    ?? (property_exists($this, 'viewPermission') ? $this->viewPermission : null))
        );

        if ($permission) {
            goIfUserCan($permission);
        }

        $request = request();
        $request->validate($rules);

        $model = $id ? $this->model::findOrFail($id) : new $this->model;

        if (method_exists($this, 'beforeDataSave')) {
            $this->beforeDataSave($request, $model, $id);
        } else {
            foreach (array_keys($rules) as $key) {
                $model->$key = $request->$key;
            }
        }

        $model->save();

        if (method_exists($this, 'afterDataSave')) {
            $this->afterDataSave($request, $model, $id);
        }

        return $model;
    }
}
