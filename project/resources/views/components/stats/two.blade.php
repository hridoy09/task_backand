@props(['title', 'value', 'link' => null, 'color' => '#000000'])

<x-card class="stat stat-two">
    @if($link)
        <a class="link" href="{{ $link }}"></a>
    @endif
    <p class="mb-1" style="--color: {{ $color }} ">{{$title}}</p>
    <p>{{$value}}</p>
</x-card>
