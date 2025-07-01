<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media; 
use Spatie\Image\Enums\Fit;

class SettingMedia extends Model implements HasMedia
{
use HasFactory, InteractsWithMedia;

    public const APP_LOGO = 'app_logo';
    public const APP_FAVICON = 'app_favicon';

    protected $fillable = ['key', 'value']; // Add any other relevant fields

    public function registerMediaCollections(): void
    {
        if($this->key == SettingMedia::APP_LOGO) {
            $this->addMediaCollection(config('settings.general.app_logo.collection'))
                ->singleFile(); // Optional: ensures only one file can be in this collection
        } else if ($this->key == SettingMedia::APP_FAVICON) {
            $this->addMediaCollection(config('settings.general.app_favicon.collection'))
                ->singleFile(); // Optional: ensures only one file can be in this collection
        }
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        //if($this->key == SettingMedia::APP_LOGO) {
        /*$this->addMediaConversion(config('settings.general.app_logo.alias')) // You can name your conversion 'thumb', 'logo_150x40', etc.
            ->fit(Fit::Crop, config('settings.general.app_logo.width'), config('settings.general.app_logo.height')) // <--- Updated call 
             ->nonQueued(); // For smaller images like logos, you might not need to queue it.
                             // Remove nonQueued() for larger images or if you have a queue worker.*/
        //} else if($this->key == SettingMedia::APP_FAVICON) {
        $this->addMediaConversion(config('settings.general.app_favicon.alias')) // You can name your conversion 'thumb', 'logo_150x40', etc.
            ->fit(Fit::Crop, config('settings.general.app_favicon.width'), config('settings.general.app_favicon.height')) // <--- Updated call 
             ->nonQueued(); // For smaller images like logos, you might not need to queue it.
                             // Remove nonQueued() for larger images or if you have a queue worker.
        //}
    }
}
