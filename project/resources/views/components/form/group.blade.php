@props([
    'for' => '',
    'label' => '',
    'help' => false,
])

<div class="form-group">
    @if ($label)
        <label for="{{ $for }}" class="form-label">
            {{ __($label) }}
        </label>
    @endif

    {{ $slot }}

    @error($for)
        <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror
</div>

@pushOnce('styles')
    <style>
        span.help-qs {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            font-size: 11px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 50%;
            height: 1.2rem;
            width: 1.2rem;
            border: 1px solid #c0c0c0;
            color: var(--text-color);
            position: relative;
        }

        span.help-qs::after {
            content: attr(data-qs);
            position: absolute;
            max-width: 200px;
            left: 50%;
            bottom: 130%;
            transform: translateX(-50%);
            background: #333;
            color: #fff;
            font-size: 12px;
            padding: 5px 8px;
            border-radius: 4px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease-in-out;
            pointer-events: none;
            z-index: 10;
        }

        span.help-qs::after {
            content: attr(data-qs);
            position: absolute;
            max-width: 300px;
            /* control tooltip width */
            left: 50%;
            bottom: 130%;
            transform: translateX(-50%);
            background: #333;
            color: #fff;
            font-size: 12px;
            padding: 5px 8px;
            border-radius: 4px;
            white-space: normal;
            /* ✅ allow line breaks */
            word-wrap: break-word;
            /* ✅ break long words if needed */
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease-in-out;
            pointer-events: none;
            z-index: 10;
            text-align: left;
            /* looks cleaner for multiple lines */
        }


        span.help-qs:hover::after,
        span.help-qs:hover::before {
            opacity: 1;
            visibility: visible;
        }
    </style>
@endPushOnce
