<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class WorkProject extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'location',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
    }

    public function thumbnail(): ?Media
    {
        return $this->getFirstMedia('gallery');
    }

    public function galleryImages()
    {
        return $this->getMedia('gallery');
    }

    public function title(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function description(): string
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }
}
