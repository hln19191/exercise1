<?php

namespace App\Http\Controllers\Settings;

use App\Abstracts\Http\Controller as BaseController;
//use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\GeneralSettingRequest;
use App\Models\SettingMedia;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Foundation\Http\FormRequest;
class GeneralSettingController extends BaseController
{
    public function edit(GeneralSettings $settings) 
    {
        return $this->form($settings);
    }

    public function form(GeneralSettings $settings)
    {
        $appLogo = null;
        if ($settings->app_logo) {
            $settingMedia = SettingMedia::where('key','app_logo')->orderBy('id','DESC')->first();
            if ($settingMedia) {
                $appLogo = $settingMedia->getFirstMediaUrl(config('settings.general.app_logo.collection'),config('settings.general.app_logo.alias')); // 'logos' is your collection name
            }
        }

        $appFav = null;
        if ($settings->app_favicon) {
            $settingMedia = SettingMedia::where('key','app_favicon')->orderBy('id','DESC')->first();
            if ($settingMedia) {
                $appFav = $settingMedia->getFirstMediaUrl(config('settings.general.app_favicon.collection'),config('settings.general.app_favicon.alias')); // 'logos' is your collection name
            }
        }

        return Inertia::render('Settings/General/Form',['settings' => $settings,'appLogo' => $appLogo,'appFav' => $appFav]);
    }

    public function update(GeneralSettingRequest $request,GeneralSettings $settings)
    {
        $validated = $request->validated();

        try {
            $settings->app_name = $validated['app_name'];

            if ($request->hasFile('app_logo')) {
                
                $settingMedia = new SettingMedia();
                $settingMedia->key = 'app_logo'; //
                $settingMedia->save();
                $media = $settingMedia->addMediaFromRequest('app_logo')
                                 ->toMediaCollection(config('settings.general.app_logo.collection')); // 'logos' is a media collection
        
                $settingMedia->value = $media->id;
                $settingMedia->save();
                $settings->app_logo = $media->id;

            } else if($request->input('clear_app_logo')) {
                if ($settings->app_logo) {
                    $oldLogo = SettingMedia::where('key','app_logo')->first();
                    if ($oldLogo) {
                        $oldLogo->delete();
                    }
                }
                $settings->app_logo = "";
            }

            if ($request->hasFile('app_fav')) {
                
                $settingMedia = new SettingMedia();
                $settingMedia->key = 'app_favicon'; //
                $settingMedia->save();
                $media = $settingMedia->addMediaFromRequest('app_fav')
                                 ->toMediaCollection(config('settings.general.app_favicon.collection')); // 'logos' is a media collection
        
                $settingMedia->value = $media->id;
                $settingMedia->save();
                $settings->app_favicon = $media->id;

            } else if($request->input('clear_app_fav')) {
                if ($settings->app_favicon) {
                    $oldLogo = SettingMedia::where('key','app_favicon')->first();
                    if ($oldLogo) {
                        $oldLogo->delete();
                    }
                }
                $settings->app_favicon = "";
            }

            $settings->save();

            $messageKey = 'data_is_updated';
            $name = trans_choice('general.general_settings',2);
            $message = __('general.' . $messageKey, ['name' => $name]);

        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        return redirect()->route('settings.general.edit')->with('success', $message);
    }
}