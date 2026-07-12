@props(['body_class' => ''])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @isset($header)
        <div class="card-header">
            @php echo $header @endphp
        </div>
    @endisset
    
    <div class="{{ $body_class . ' card-body' }}">
        {{ $slot }}
    </div>
</div>
