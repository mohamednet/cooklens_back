<?php

namespace App\Features\Recipes\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class RecipeMediaService
{
    /**
     * Upload recipe image.
     */
    public function uploadImage(UploadedFile $file, string $folder = 'recipes'): string
    {
        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $folder . '/' . $filename;

        // Resize and optimize image
        $image = Image::read($file);
        $image->scale(width: 1200);
        
        // Save to storage
        Storage::disk('public')->put($path, (string) $image->encode());

        return Storage::url($path);
    }

    /**
     * Upload recipe video.
     */
    public function uploadVideo(UploadedFile $file, string $folder = 'recipes/videos'): string
    {
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, 'public');

        return Storage::url($path);
    }

    /**
     * Delete media file.
     */
    public function deleteMedia(string $url): bool
    {
        $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));

        return Storage::disk('public')->delete($path);
    }

    /**
     * Upload step image.
     */
    public function uploadStepImage(UploadedFile $file): string
    {
        return $this->uploadImage($file, 'recipes/steps');
    }

    /**
     * Upload ingredient image.
     */
    public function uploadIngredientImage(UploadedFile $file): string
    {
        return $this->uploadImage($file, 'ingredients');
    }
}
