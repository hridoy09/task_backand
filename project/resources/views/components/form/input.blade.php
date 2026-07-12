@props([
    'name',
    'value' => null,
    'id' => null,
    'type' => 'text'
])

<input 
    type={{ $type }} 
    name="{{ $name }}" 
    id="{{ $id ?? $name }}"
    value="{{ old($name, $value) }}"
    {{ $attributes->merge(['class' => 'form-control']) }} 
/>
