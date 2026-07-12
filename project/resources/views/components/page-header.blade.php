@props([
    'page_title' => '',
    'back_route' => null,
    'add_route'  => null,
    'search' => false
])

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <h3 class="mb-3">
        {{ $page_title }}
    </h3>

    <div class="d-flex gap-2">
        {{ $slot }}

        <div class="d-flex gap-2">
            @if ($search)
                <x-admin-search />
            @endif
            
            @isset($add_route)
                <x-button href="{{ $add_route }}">
                    <x-icons.add />
                    @lang('Add New')
                </x-button>
            @endisset

            @isset($back_route)
                <x-button href="{{ $back_route }}">
                    <x-icons.back-v1 />
                    @lang('Back')
                </x-button>
            @endisset
        </div>
    </div>
</div>
