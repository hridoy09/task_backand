@php
    $rec     = integration('recaptcha');
    $enabled = (bool) data_get($rec, 'enabled');
    $type    = data_get($rec, 'settings.type', 'v2_checkbox');
    $siteKey = data_get($rec, 'settings.site_key');
@endphp

@if($enabled && $siteKey)
    @if($type === 'v2_checkbox')
        <div class="mb-3">
            <div class="g-recaptcha" data-sitekey="{{ $siteKey }}"></div>
            @error('g-recaptcha-response')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        @push('scripts')
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endpush
    @else
        <input type="hidden" name="g-recaptcha-response" class="recaptcha-token">
        @push('scripts')
            <script src="https://www.google.com/recaptcha/api.js?render={{ $siteKey }}"></script>
            <script>
            document.addEventListener('submit', function(e){
                const form = e.target.closest('form');
                if(!form) return;

                const tokenInput = form.querySelector('.recaptcha-token');
                if(!tokenInput) return;

                e.preventDefault();
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ $siteKey }}', {action: 'submit'}).then(function(token) {
                        tokenInput.value = token;
                        form.submit();
                    });
                });
            }, true);
            </script>
        @endpush
    @endif
@endif
