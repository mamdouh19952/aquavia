<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService
{
    /**
     * Upload a file to a model's media collection.
     *
     * @param Model $model
     * @param UploadedFile $file
     * @param string $collection
     * @return Media
     */
    public function upload(Model $model, UploadedFile $file, string $collection = 'default'): Media
    {
        return $model->addMedia($file)->toMediaCollection($collection);
    }

    /**
     * Update a model's media (replace old media with new file).
     *
     * @param Model $model
     * @param UploadedFile $file
     * @param Media|null $oldMedia
     * @param string $collection
     * @return Media
     */
    public function update(Model $model, UploadedFile $file, ?Media $oldMedia = null, string $collection = 'default'): Media
    {
        if ($oldMedia) {
            $oldMedia->delete();
        }

        return $this->upload($model, $file, $collection);
    }

    /**
     * Delete all media from a collection.
     *
     * @param Model $model
     * @param string $collection
     * @return void
     */
    public function deleteMedia(Model $model, string $collection = 'default'): void
    {
        $model->clearMediaCollection($collection);
    }

    /**
     * Delete a specific media item.
     *
     * @param Media $media
     * @return void
     */
    public function deleteMediaItem(Media $media): void
    {
        $media->delete();
    }
}
