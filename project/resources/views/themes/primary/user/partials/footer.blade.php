<footer class="footer-area">

    <!-- bottom Footer -->
    <div class="bottom-footer py-3">
        <div class="container">
            <div class="text-center">
                <p class="bottom-footer__text"> @lang('Copyright') &copy; {{ date('Y') }} <a href="{{ route('home') }}" class="bottom-footer__link">{{ generalSetting('site_title') }}</a> - @lang('all rights
                    reserved'). <br>
                    {{-- | --}}
                    @foreach (\App\Models\Page::where('privacy', 1)->get() as $page)
                        <a href="{{ route('site.page', $page->slug) }}" class="bottom-footer__link">
                            {{ __($page->title) }}
                        </a> @if(!$loop->last) | @endif
                    @endforeach
                </p>
            </div>
        </div>
    </div>
</footer>
