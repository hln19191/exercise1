<?php

use App\Models\SettingMedia;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Auth;

if (! function_exists('user')) {
    /**
     * Get the authenticated user.
     *
     * @return \App\Models\Auth\User
     */
    function user() : User
    {
        return Auth::user() ?? [];
    }
}


if (! function_exists('get_logo')) {
    /**
     * Get the authenticated user.
     *
     * @return string
     */
    function get_logo() : string
    {
        $app_logo = config('settings.general.app_logo.default'); //get default

        $settings = app(GeneralSettings::class);

        if ($settings && $settings->app_logo) {
            $media = SettingMedia::where('key',config('settings.general.app_logo.name'))->orderBy('id','DESC')->first();
            if ($media) {
                $app_logo = $media->getFirstMediaUrl(config('settings.general.app_logo.collection'),config('settings.general.app_logo.alias')); // 'logos' is your collection name
            }
        }
        
        return $app_logo;
    }
}

if (! function_exists('get_favicon')) {
    /**
     * Get the authenticated user.
     *
     * @return string
     */
    function get_favicon() : string
    {
        $app_favicon = config('settings.general.app_favicon.default'); //get default
        //')'/assets/images/default-favicon.ico';
        $settings = app(GeneralSettings::class);

        if ($settings && $settings->app_favicon) {
            $media = SettingMedia::where('key',config('settings.general.app_favicon.name'))->orderBy('id','DESC')->first();
            if ($media) {
                $app_favicon = $media->getFirstMediaUrl(config('settings.general.app_favicon.collection'),config('settings.general.app_favicon.alias')); // 'logos' is your collection name
            }
        }
        
        return $app_favicon;
    }
}