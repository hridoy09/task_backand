<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function builder($slug)
    {
        goIfUserCan('view-forms');

        $title = __('Form Builder');
        $form  = Form::where('slug', $slug)->firstOrFail();
        return view('admin.setting.form.builder', compact('title', 'form'));
    }

    public function saveBuilder(Request $request, $id) 
    {
        goIfUserCan('save-forms');

        $request->validate([
            'form_data' => 'required'
        ]);

        $form = Form::findOrFail($id);
        $form->form_data = $request->form_data;
        $form->save();

        return back()->withSuccess(__('Form data saved successfully'));
    }
    
    public function index()
    {
        goIfUserCan('view-forms');

        $title = __('Forms');
        $forms = Form::paginate();

        return view('admin.setting.form.index', compact('title', 'forms'));
    }

    public function save(Request $request, $id = null) 
    {
        goIfUserCan('save-forms');

        $request->validate([
            'name' => 'required|unique:forms',
        ]);

        if($id) {
            $form = Form::where('id', $id)->where('default', 1)->find($id);

            if($form) {
                return back()->withErrors(__('You canot edit this form'));
            }
        }
        
        $form              = new Form();
        $form->name        = $request->name;
        $form->slug        = str()->slug($request->name);
        // $form->default     = 1;
        $form->description = $request->description;
        $form->save();

        return back()->withSuccess(__('Form saved successfully'));
    }
}
