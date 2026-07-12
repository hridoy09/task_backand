@props(['name','id'=>$name,'required'=>false])
<div class="w-100">
    <select name="{{ $name }}" id="{{ $id }}" {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'form-select']) }}>
        {{ $slot }}
    </select>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
