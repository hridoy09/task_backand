<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\Form;

class DynamicFormHandler
{
    protected $form;
    protected $fields;

    public function __construct(string $slug)
    {
        $this->form = Form::where('slug', $slug)->firstOrFail();
        $this->fields = json_decode($this->form->form_data) ?? [];
        // dd($this->fields); // optional debug
    }

    /**
     * Validate the request against the dynamic form fields
     */
    public function validate(Request $request)
    {
        $rules = [];
        $fileFields = [];

        foreach ($this->fields as $fieldObj) {
            $field = (array) $fieldObj; // cast object to array
            $name = $field['name'] ?? null;
            if (!$name) continue;

            switch ($field['type'] ?? '') {
                case 'text':
                case 'textarea':
                    $rules[$name] = !empty($field['required']) ? 'required|string|max:255' : 'nullable|string|max:255';
                    break;

                case 'number':
                    $rules[$name] = !empty($field['required']) ? 'required|numeric' : 'nullable|numeric';
                    if (!empty($field['min'])) $rules[$name] .= '|min:' . $field['min'];
                    if (!empty($field['max'])) $rules[$name] .= '|max:' . $field['max'];
                    break;

                case 'date':
                    $rules[$name] = !empty($field['required']) ? 'required|date' : 'nullable|date';
                    break;

                case 'select':
                case 'radio-group':
                    $rules[$name] = !empty($field['required']) ? 'required' : 'nullable';
                    break;

                case 'checkbox-group':
                    $rules[$name] = !empty($field['required']) ? 'required|array' : 'nullable|array';
                    break;

                case 'file':
                    $fileFields[] = $name;
                    $rules[$name] = !empty($field['required']) ? 'required|file' : 'nullable|file';
                    if (!empty($field['mimes'])) {
                        $rules[$name] .= '|mimes:' . implode(',', $field['mimes']);
                    }
                    if (!empty($field['max'])) {
                        $rules[$name] .= '|max:' . $field['max'];
                    }
                    break;
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Handle file uploads
        foreach ($fileFields as $fieldName) {
            if ($request->hasFile($fieldName)) {
                $data[$fieldName] = $request->file($fieldName)->store('forms/' . $fieldName, 'public');
            }
        }

        return $data;
    }

    /**
     * Get the original form fields
     */
    public function getFields()
    {
        return $this->fields;
    }
}