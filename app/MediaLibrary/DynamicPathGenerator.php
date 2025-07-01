<?php

namespace App\MediaLibrary;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DynamicPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        // Get the model type: App\Models\User → user
        $model = class_basename($media->model_type);
        $folder = Str::kebab(Str::plural($model)); // e.g., users, assets

        return 'uploads/'.$folder . '/' .   $media->model_id . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive/';
    }
}
