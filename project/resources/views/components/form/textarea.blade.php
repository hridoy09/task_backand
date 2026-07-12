@props([
    'name',
    'id' => $name,
    'rows' => 4,
    'required' => false,
])

<textarea
    name="{{ $name }}"
    id="{{ $id }}"
    rows="{{ $rows }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'form-control form-textarea']) }}
>{{ $slot ?? old($name) }}</textarea>
