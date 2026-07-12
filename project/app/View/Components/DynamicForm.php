<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Form; 

class DynamicForm extends Component
{
    public $form;

    /**
     * Create a new component instance.
     */
    public function __construct(public string $slug)
    {
        $this->form = Form::where('slug', $slug)->first();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dynamic-form', [
            'form' => $this->form,
            'fields' => $this->form ? json_decode($this->form->form_data, true) : []
        ]);
    }
}
