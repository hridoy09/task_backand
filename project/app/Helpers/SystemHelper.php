<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\SystemNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class SystemHelper
{
    private $gs = null;

    public $name = 'LaraKit';

    public $version = '1.0.0';

    public $licenseUrl = 'https://linceurl.com/license';

    public function __construct()
    {
        $this->gs = generalSetting();
    } 

    /**
     * Notify by email for now against the `User` Model
     * @param \App\Models\User $user
     * @param string $subject
     * @param string $view
     * @param array $data
     * @return void
     */
    public static function notify($user, $subject, $view, $data = [])
    {
        try {
            Mail::to($user->email)->send(
                new SystemNotification($subject, $view, $data, $user)
            );
        } catch (\Throwable $th) {
            info($th->getMessage());
        }
    }

    /**
     * Get the dynamic sections array
     * @return array
     */
    public static function sections(): array
    {
        $data = json_decode(file_get_contents(resource_path(
            'json/sections.json'
        )), true );

        return array_filter($data, function($item) {
            return !boolval($item['fixed']);
        });
    }

    /**
     * Get the list of all the currencies available
     * @param mixed $systemCurrency
     */
    public function currencies($systemCurrency = false)
    {
        $currenceis = json_decode(file_get_contents(resource_path(
            'json/currencies.json'
        )), true);

        if ($systemCurrency) {
            return collect($currenceis)->where('code', generalSetting('currency'))->first();
        }

        return $currenceis;
    }

    /**
     * Return the amount with symbol prexi & currency suffix
     * @param mixed $amount
     * @return string
     */
    public function amountWithCurrency($amount = 0)
    {
        $symbol = $this->currencies(true)['symbol'];


        $formatted = Number::format($amount ?? 0, 2, 2, app()->getLocale());

        return (
            html_entity_decode($symbol) .
            $formatted .
            ' ' . generalSetting('currency')
        );
    }

    /**
     * Clear and optimize the system
     * @return void
     */
    public function clearCache()
    {
        Artisan::call("optimize:clear");
    }

    /**
     * Get the logo iamge path of the system
     * @return string
     */
    public static function logo(bool $is_dark = false): string
    {
        return imageSrc(generalSetting('site_logo' . ($is_dark ? '_dark' : '')));
    }

    /**
     * Get the favicon path of the system
     * @return string
     */
    public static function favicon(): string
    {
        return imageSrc(generalSetting('site_favicon'));
    }


    public function googleCaptchaEnabled()
    {
        return integrationEnabled('recaptcha');
        return generalSetting('google_recaptcha_enabled');
    }

    public function getDateTime($dateTime = null, $format = null)
    {
        if (!$dateTime) $dateTime = now();

        if (!$format) $format = 'd/m/Y h:i:a';

        return Carbon::parse($dateTime)->locale(app()->getLocale())->format($format);
    }

    public function colors()
    {
        return [
            'Fawn'          => '#E4AA7A',
            'ChineseViolet' => '#886288',
            'Tuscany'       => '#CE998E',
            'Jasmine'       => '#FFDB87',
            'Citron'        => '#89A823',
            'Russet'        => '#79481C',
            'Bittersweet'   => '#FE6F5E',
            'Shandy'        => '#FBE274',
            'PersianPink'   => '#EF85BB',
            'CyanAzure'     => '#5184BA',
            'Onyx'          => '#33363B',
        ];
    }
}
