<footer class="px-4 my-0 mb-0 text-center text-muted admin-footer">
    <div class="card p-2 pt-3 border-0 mb-0">
        <div class="d-flex justify-content-between flex-wrap">
            <p>
                @lang('Copyrights') {{ date('Y') }} © {{ generalSetting('site_title') }} . @lang('Version') : <strong>{{ software()->version }}</strong>
            </p>

            <p>
                @lang('Page loaded in') {{ round(microtime(true) - LARAVEL_START, 2) }}s
            </p>
        </div>
    </div>
</footer>
