@props(['name', 'id' => null, 'small' => false])

<label class="cfg-switch mb-0 {{ $small ? 'small' : '' }}">
    <input 
        type="checkbox" 
        id="{{ $id ?? $name }}" 
        name="{{ $name }}" 
        {{ $attributes->merge(['class' => '']) }}
        @checked($checked ?? false)
    >
    <span class="cfg-slider"></span>
</label>
