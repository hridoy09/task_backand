@props([
    'path' => '',
    'id' => 'image',
    'label' => __('Upload Image'),
    'size' => '',
    'accept' => 'image/*',
    'required' => false,
    'name' => 'image',
    'dark' => false,
])

<div class="upload-item">
    <div class="upload-item__preview @if($dark) dark @endif">
        <img class="preview" src="{{ $path }}" alt="">
    </div>
    <div class="upload-item__trigger">
        <input type="file" id="{{ $id }}" name="{{ $name }}" accept="{{ $accept }}" />
        <label for="{{ $id }}">{{ $label }}</label>
        @if ($size)
            <span class="upload-item__info">@lang('Recommended size:') {{ $size }}</span>
        @endif
    </div>
</div>

@push('scripts')
<script>
$(document).on('change', 'input[type="file"]', function (event) {
    const input = this;
    const $input = $(input);
    const file = input.files && input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        $input.closest('.upload-item').find('.preview').attr('src', e.target.result);
    };
    reader.readAsDataURL(file);
});
</script>
@endpush


@push('styles')
<style>
    .upload-item__preview.dark {
        background: #333!important;
    }
</style>
    
@endpush