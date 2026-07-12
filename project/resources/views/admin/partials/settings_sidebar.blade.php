@php
    $settingsSidebarItems = settings_sidebar_items();
@endphp

<ul class="manage-section-list">

    @foreach ($settingsSidebarItems as $item)
        @php
            $routeName = $item['route'];
            $activePattern = $item['active'] ?? $routeName;
            $title = __($item['title']);
            $iconComponent = 'icons.' . ($item['icon'] ?? 'setting');
            $url = route($routeName);
        @endphp
        <li class="manage-section-list__item  {{ activeClass($activePattern) }}">
            <a href="{{ $url }}" class="manage-section-list__link"
                data-spotlight-link="true" data-spotlight-text="{{ $title }}"
                data-spotlight-url="{{ $url }}" data-spotlight-icon="{{ $item['icon'] ?? 'setting' }}">
                <span class="manage-section-list__link-icon">
                    <x-dynamic-component :component="$iconComponent" class="settings-icon" />
                </span>
                <span class="manage-section-list__link-text">{{ $title }}</span>
            </a>
        </li>
    @endforeach
</ul>


