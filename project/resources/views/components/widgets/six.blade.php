    @props(['title', 'value', 'icon', 'shape', 'link' => null])


    <div class="widget-item six">
        @if($link)
            <a href="{{ $link }}" class="widget-item__link"></a>
        @endif
        <div class="widget-item__content">
            <p class="widget-item__title">{{$title}}</p>
            <h3 class="widget-item__number mb-0">{{ $value }}</h3>
        </div>
        <div class="widget-item__thumb">
            <span class="widget-item__icon">
                <x-dynamic-component :component="'widget_icons.' . $icon" />
            </span>
        </div>
    </div>
