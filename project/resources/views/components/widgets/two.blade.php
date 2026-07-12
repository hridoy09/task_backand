@props(['title', 'value', 'icon', 'shape', 'link' => null])

<div class="widget-item two">
    <a @if ($link) href="{{ $link }}" @endif class="widget-item__link"></a>
    <div class="widget-item__thumb">
        <span class="widget-item__icon">
            <x-dynamic-component :component="'widget_icons.' . $icon" />
        </span>
    </div>
    <div class="widget-item__content">
        <p class="widget-item__title">{{ $title }}</p>
        <h3 class="widget-item__number mb-0">{{ $value }}</h3>
    </div>
    <span class="widget-item__shape">
        <x-dynamic-component :component="'widget_icons.' . $icon" opacity="0.06" height="120" width="120" />
        
    </span>
</div>
