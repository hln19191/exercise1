<?php

namespace App\Settings;

use Spatie\MediaLibrary\HasMedia;
use Spatie\LaravelSettings\Settings;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media; // <--- ADD THIS IMPORT

class GeneralSettings extends Settings 
//implements HasMedia
{
    //#[CastWith(ArraySettingsCast::class)] // <--- THIS LINE IS THE PROBLEM
    //public array $my_misconfigured_array; 

    //https://github.com/spatie/laravel-settings/discussions/185
    //use InteractsWithMedia;

    public string $app_name; // This is where you defined 'app_name'
    public string $app_logo; // This is where you defined 'app_logo'
    //public ?int $app_logo_id; // This is where you defined 'app_logo'
    public string $app_favicon; // This is where you defined 'app_favicon'

    // ...
    //#[CastWith(ArraySettingsCast::class, IntSettingsCast::class)] // <--- FIX HERE
    //public array $my_problematic_array_property;


    public static function group(): string
    {
        return 'general';
    }

    
    // Register a media collection for your app logo
    /*
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('_app_logo')
            ->singleFile(); // Ensures only one logo can be associated
    }

    // You might want a helper to easily get the logo URL
    public function getAppLogo(): ?string
    {
        return $this->getFirstMediaUrl('_app_logo');
    }*/

    // When converting to array for Inertia, we want to include the logo URL.
    // This is optional if you always call getAppLogoUrl() in frontend
    // but makes it convenient.
    /*public function toArray(): array
    {
        $array = parent::toArray();
        $array['app_logo'] = $this->getAppLogo();
        //$array['app_logo_thumb_url'] = $this->getAppLogoThumbUrl();
        return $array;
    }*/
}