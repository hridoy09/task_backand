    @props(['title', 'value', 'icon', 'shape', 'link' => null])

    <div class="widget-item four">
        <div class="widget-item__thumb">
            <span class="widget-item__icon">
                <x-dynamic-component :component="'widget_icons.' . $icon" />
            </span>
        </div>
        <div class="widget-item__content">
            <p class="widget-item__title">{{ $title }}</p>
            <h5 class="widget-item__number mb-0">{{ $value }}</h5>
        </div>
        @if ($link)
            <a href="#" class="widget-item__viewlink">@Lang('View All')</a>
        @endif

    </div>
