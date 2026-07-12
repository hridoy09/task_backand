<div class="d-flex flex-column flex-md-row align-items-start mb-3 align-items-md-center justify-content-between w-100">
    @isset($title)
        <h3 class="mb-2 mb-md-0">{{ $title }}</h3>
    @endisset

    @isset($right)
        <div class="d-flex align-items-center gap-2 ms-md-auto">
            {{ $right ?? '' }}
        </div>
    @endisset
</div>
