<?php

use App\Helpers\SystemHelper;
use App\Models\Admin;
use App\Models\BlogPost;
use App\Models\GeneralSetting;
use App\Models\Integration;
use App\Models\Page;
use App\Models\PageView;
use App\Services\FileManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

function recaptcha_valid(?string $token, ?string $ip = null): bool
{
    $rec = integration('recaptcha'); // your earlier helper that fetches Integration row
    if (! $rec || ! data_get($rec, 'enabled')) {
        // If not enabled, treat as pass (no recaptcha in use)
        return true;
    }

    $type = data_get($rec, 'settings.type', 'v2_checkbox'); // v2_checkbox | v3_score
    $secret = data_get($rec, 'settings.secret_key');
    $minScore = (float) data_get($rec, 'settings.score', 0.5);

    if (! $secret || ! $token) {
        return false;
    }

    try {
        $resp = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $ip,
        ])->json();

        if (! ($resp['success'] ?? false)) {
            return false;
        }

        if ($type === 'v3_score') {
            $score = (float) ($resp['score'] ?? 0);

            return $score >= $minScore;
        }

        // v2 checkbox
        return true;
    } catch (\Throwable $e) {
        // on network error, fail closed (safer)
        return false;
    }
}

function integration(string $key)
{
    static $cache = [];
    if (! array_key_exists($key, $cache)) {
        $cache[$key] = Integration::where('key', $key)->first();
    }

    return $cache[$key];
}

function integrationEnabled($key)
{
    return boolval(integration($key));
}

function getBadge(
    $text,
    $class = '',
    $color = '',
) {
    if ($color) {
        return '<span class="badge" style="background-color: ' . $color . ';">' . __($text) . '</span>';
    }

    return '<span class="badge badge-' . $class . '">' . __($text) . '</span>';
}

function countries()
{
    return json_decode(file_get_contents(resource_path(
        'json/countries.json'
    )), true);
}

if (! function_exists('get_seo_content')) {
    function get_seo_content(string $slug, bool $isBlog = false)
    {
        $seoContent = $isBlog
            ? BlogPost::where('slug', $slug)->value('seo_content')
            : Page::where('slug', $slug)->value('seo_content');

        if (is_string($seoContent)) {
            $seoContent = json_decode($seoContent, false);
        } elseif (is_array($seoContent)) {
            $seoContent = (object) $seoContent;
        }

        $globalSeo = generalSetting('global_seo') ?? [];
        if (is_string($globalSeo)) {
            $globalSeo = json_decode($globalSeo, false);
        } elseif (is_array($globalSeo)) {
            $globalSeo = (object) $globalSeo;
        }

        $seoContent = (object) array_merge(
            (array) $globalSeo,
            (array) $seoContent
        );

        if (isset($seoContent->meta_keywords) && is_array($seoContent->meta_keywords)) {
            $seoContent->meta_keywords = implode(',', $seoContent->meta_keywords);
        }

        return $seoContent;
    }
}

function goIfUserCan($ability, $context = null): void
{
    if (! userCan($ability, $context)) {
        abort(403, __('You do not have permission to perform this action.'));
    }
}

function userCan($ability = null, $context = null): bool
{
    if (is_null($ability)) {
        return true;
    }

    $admin = admin();

    if (! $admin) {
        return false;
    }

    if (method_exists($admin, 'isAn') && $admin->isAn('super-admin')) {
        return true;
    }

    if (is_array($ability)) {
        foreach ($ability as $candidate) {
            if (userCan($candidate, $context)) {
                return true;
            }
        }

        return false;
    }

    $abilityName = strtolower(trim($ability));

    if ($context) {
        $variants = [];

        if (is_string($context)) {
            $context = trim($context);
            if ($context !== '') {
                $variants[] = "{$abilityName}-{$context}";
                $variants[] = "{$abilityName}.{$context}";
                $variants[] = "{$abilityName}_{$context}";
            }
        }

        foreach ($variants as $variant) {
            if ($admin->can($variant)) {
                return true;
            }
        }
    }

    return $admin->can($abilityName);
}

if (! function_exists('filePath')) {
    function filePath($key)
    {
        $sizes = fileSizes($key);

        return $sizes['path'] ?? null;
    }
}

if (! function_exists('uploadImageSize')) {
    function uploadImageSize($key)
    {
        $sizes = fileSizes($key);

        return $sizes['size'] ?? null;
    }
}

function imageSrc($image)
{
    if (! $image) {
        return asset('no-image.png');
    }

    return \App\Services\FileManager::getPublicUrl($image);
}

function imageSize($key)
{
    $size = config('files.' . $key . '.size', null);
    if (! $size) {
        return '';
    }

    return  $size;
}

if (! function_exists('fileSizes')) {
    function fileSizes($key = null)
    {
        $data = config('files');

        if ($key) {
            return $data[$key] ?? [];
        }

        return $data;
    }
}

function software()
{
    return new SystemHelper;
}

function getRealIP()
{
    if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ipList[0]);
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    return $ip;
}

/**
 * Get the general setting information
 *
 * @param  mixed  $key
 */
function generalSetting($key = null)
{
    try {
        DB::connection()->getPdo();
    } catch (\Exception $e) {
        return null;
    }

    try {
        $generalSetting = Cache::rememberForever('general-setting', function () {
            return GeneralSetting::first();
        });
    } catch (\Throwable $e) {
        return null;
    }

    if ($key) {
        return $generalSetting?->$key;
    }

    return $generalSetting;
}

/**
 * Get the admin user
 */
function admin()
{
    return auth('admin')->user();
}

function generateTransactionId(string $prefix = 'txn_', int $length = 12): string
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random = '';
    $max = strlen($characters) - 1;

    for ($i = 0; $i < $length; $i++) {
        $random .= $characters[random_int(0, $max)];
    }

    return $prefix . $random;
}

function amount($amount = 0)
{
    return $amount;
}

function getClassesInNamespace(string $namespace): array
{
    $classes = [];
    foreach (get_declared_classes() as $class) {
        if (Str::startsWith($class, $namespace)) {
            $classes[] = class_basename($class);
        }
    }

    $classMap = require base_path('vendor/composer/autoload_classmap.php');

    foreach ($classMap as $class => $path) {
        if (Str::startsWith($class, $namespace) && class_exists($class)) {
            $reflection = new ReflectionClass($class);
            if (! $reflection->isAbstract()) {
                $classes[] = class_basename($class);
            }
        }
    }

    return array_unique($classes);
}

if (! class_exists('System')) {
    class_alias(\App\Facades\System::class, 'Setting');
}

if (! function_exists('activeClass')) {
    /**
     * Returns 'active' if the current route name and parameters match.
     *
     * @param  array|string|null  $params
     */
    function activeClass(string $routeName, $params = null, $className = 'active'): string
    {
        if (! request()->routeIs($routeName)) {
            return '';
        }

        if (is_null($params)) {
            return $className;
        }

        // If a single parameter (like a slug) is passed
        if (! is_array($params)) {
            $params = [$params];
        }

        $currentParams = request()->route()->parameters();

        foreach (array_values($params) as $index => $value) {
            if (! isset(array_values($currentParams)[$index]) || array_values($currentParams)[$index] != $value) {
                return '';
            }
        }

        return $className;
    }
}

function bytesToHumanReadable($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));                             // $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * This will count page visit but won't count same in 1 hour
 */
if (! function_exists('count_page_view')) {
    function count_page_view($slug = '/')
    {
        $key = 'visited_page_' . $slug . '_' . request()->ip();
        if (! cache()->has($key)) {
            PageView::updateOrCreate(
                ['slug' => $slug],
                ['views' => DB::raw('views + 1')]
            );
            cache()->put($key, true, now()->addMinutes(60));
        }
    }
}

function handleResize($key)
{
    $sizeStr = uploadImageSize($key);

    if (! $sizeStr) {
        return null;
    }

    [$width, $height] = explode('x', $sizeStr);
    $resize = ['width' => (int) $width, 'height' => (int) $height];

    return $resize;
}

function viewShare($theView, array $vars = [])
{
    view()->composer($theView, function ($view) use ($vars) {
        foreach ($vars as $key => $value) {
            $view->with($key, $value);
        }
    });
}

function theme($view, ...$rest)
{
    return view('theme::' . $view, ...$rest);
}

if (!function_exists('settings_sidebar_items')) {
    function settings_sidebar_items(): array
    {
        static $items;

        if ($items !== null) {
            return $items;
        }

        $path = resource_path('json/settings_sidebar.json');

        if (! is_file($path)) {
            return $items = [];
        }

        $decoded = json_decode(file_get_contents($path), true);

        if (! is_array($decoded)) {
            return $items = [];
        }

        return $items = array_values(array_filter($decoded, function ($item) {
            return isset($item['title'], $item['route']);
        }));
    }
}

if (!function_exists('admin_notification_recipients')) {
    function admin_notification_recipients(): array
    {
        static $emails;

        if ($emails !== null) {
            return $emails;
        }

        try {
            $emails = Admin::query()
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }

        return $emails;
    }
}

if (!function_exists('ticket_priority_label')) {
    function ticket_priority_label(?int $priority): string
    {
        return [
            0 => __('Low'),
            1 => __('Normal'),
            2 => __('High'),
            3 => __('Critical'),
        ][$priority] ?? __('Unknown');
    }
}

if (!function_exists('format_amount_with_currency')) {
    function format_amount_with_currency($amount, ?string $currency = null): string
    {
        $currency = $currency ?: generalSetting('currency') ?: 'USD';

        if ($amount === null) {
            return '0 ' . $currency;
        }

        return number_format((float) $amount, 2) . ' ' . $currency;
    }
}

if (!function_exists('googleCaptchaVerify')) {
    function googleCaptchaVerify($request)
    {
        if (!integrationEnabled('recaptcha')) {
            return;
        }

        $token = $request->input('g-recaptcha-response');
        if (!recaptcha_valid($token, $request->ip())) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'g-recaptcha-response' => __('reCAPTCHA validation failed.'),
            ]);
        }
    }
}

if (!function_exists('upload_support_attachment')) {
    function upload_support_attachment(UploadedFile $file): ?string
    {
        if (!$file->isValid()) {
            return null;
        }

        try {
            return FileManager::uploadToAssets(
                $file,
                '/assets/images/support-attachments'
            );
        } catch (\Throwable $e) {
            \Log::warning('Support attachment upload failed', [
                'message' => $e->getMessage(),
                'file'    => $file->getClientOriginalName(),
            ]);

            return null;
        }
    }
}

if (!function_exists('support_attachment_url')) {
    function support_attachment_url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $normalized = ltrim($path, '/');

        if (Str::startsWith($normalized, 'assets/')) {
            return asset($normalized);
        }

        if (Str::startsWith($normalized, 'storage/')) {
            return asset($normalized);
        }

        return asset('storage/' . $normalized);
    }


    if(!function_exists('get_img')){
        function get_img($path)
        {
            if (file_exists($path)) {
                return asset($path);
            } else {
                return asset('assets/images/preview.png');
            }
        }
    }

}
