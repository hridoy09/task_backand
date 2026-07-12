@foreach ($fields as $index => $field)
    <x-form.group for="{{ 'sf-' . $index }}" label="{{ $field['label'] }}">
        <x-form.input 
            id="{{ 'sf-' . $index }}"
            required
            name="{{ $field['name'] }}" 
            type="{{ $field['type'] }}" 
            value="{{ $field['name'] == 'redirect' ? (generalSetting('app_url') . '/auth/' . $key . '/callback') : $field['value'] ?? '' }}"
        />
    </x-form.group>
@endforeach
