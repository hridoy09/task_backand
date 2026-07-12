<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Controlling;

abstract class BaseCrudController extends Controller
{
    use Controlling;

    /**
     * The model class handled by this controller.
     * Example: App\Models\Product::class
     */
    protected string $model;

    /**
     * The Blade view for listing records.
     * Example: admin.products.list
     */
    protected string $listView;

    /**
     * The Blade view for the form (create/edit).
     * If not set, defaults to "admin.{table}.form".
     */
    protected ?string $formView = null;

    /**
     * Default validation rules.
     * Example:
     * ['name' => 'required|string|unique:products,name,{id}']
     */
    protected array $rules = [];

    /**
     * Show the list page.
     */
    public function list()
    {
       
        return $this->data($this->title ?? class_basename($this->model));
    }

    /**
     * Save or update a record.
     */
    public function save($id = null)
    {
        $rules = $this->interpolateRules($id);

        $this->dataSave($id, $rules);

        return back()->withSuccess(__(
            class_basename($this->model) . ' saved successfully'
        ));
    }

    /**
     * Replace {id} placeholder in unique rules.
     */
    protected function interpolateRules($id = null): array
    {
        return collect($this->rules)->map(function ($rule) use ($id) {
            return str_replace('{id}', $id, $rule);
        })->toArray();
    }
}
