@props(['color' => false, 'link' => false, 'icon' => null, 'title' => '', 'value' => ''])

<a href="{{ $link }}" class="card stat-card">
    <div class="card-body">
        <div class="stat-icon icon-{{ $color ?? 'primary' }}"><i class="{{ $icon ?? 'fas fa-dollar-sign' }}"></i></div>
        <div>
            <h6 class="text-muted fw-normal mb-1">{{ $title ?? 'Title' }}</h6>
            <h4 class="fw-bold mb-0">{{ $value ?? '0' }}</h4>
        </div>
    </div>
</a>
