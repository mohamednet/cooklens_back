<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class FileUploadService
{
    /**
     * Upload a file to storage.
     */
    public function upload(UploadedFile $file, string $folder = 'uploads'): string
    {
        $filename = $this->generateFilename($file);
        $path = $file->storeAs($folder, $filename, 'public');

        return Storage::url($path);
    }

    /**
     * Upload multiple files.
     */
    public function uploadMultiple(array $files, string $folder = 'uploads'): array
    {
        $urls = [];

        foreach ($files as $file) {
            $urls[] = $this->upload($file, $folder);
        }

        return $urls;
    }

    /**
     * Delete a file from storage.
     */
    public function delete(string $url): bool
    {
        $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));

        return Storage::disk('public')->delete($path);
    }

    /**
     * Delete multiple files.
     */
    public function deleteMultiple(array $urls): void
    {
        foreach ($urls as $url) {
            $this->delete($url);
        }
    }

    /**
     * Generate unique filename.
     */
    protected function generateFilename(UploadedFile $file): string
    {
        return Str::uuid() . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Validate file type.
     */
    public function validateFileType(UploadedFile $file, array $allowedTypes): bool
    {
        return in_array($file->getMimeType(), $allowedTypes);
    }

    /**
     * Get file size in MB.
     */
    public function getFileSizeMB(UploadedFile $file): float
    {
        return round($file->getSize() / 1024 / 1024, 2);
    }

    /**
     * Upload image with optimization and thumbnails.
     */
    public function uploadImage(UploadedFile $file, string $directory = 'images', array $sizes = []): array
    {
        // Validate image
        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])) {
            throw new \InvalidArgumentException('Invalid image type. Only JPEG, PNG, and WebP are allowed.');
        }

        // Generate unique filename
        $filename = $this->generateFilename($file);
        
        // Store original
        $originalPath = $file->storeAs($directory, $filename, 'public');

        $urls = [
            'original' => Storage::url($originalPath),
        ];

        // Generate thumbnails if sizes provided
        foreach ($sizes as $sizeName => $dimensions) {
            try {
                $image = Image::read($file->getRealPath());
                
                // Resize maintaining aspect ratio
                if (isset($dimensions['width']) && isset($dimensions['height'])) {
                    $image->cover($dimensions['width'], $dimensions['height']);
                } elseif (isset($dimensions['width'])) {
                    $image->scale(width: $dimensions['width']);
                } elseif (isset($dimensions['height'])) {
                    $image->scale(height: $dimensions['height']);
                }

                // Generate thumbnail filename
                $thumbnailFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $sizeName . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                $thumbnailPath = $directory . '/' . $thumbnailFilename;

                // Save thumbnail
                Storage::disk('public')->put($thumbnailPath, $image->encode());
                $urls[$sizeName] = Storage::url($thumbnailPath);
            } catch (\Exception $e) {
                // If thumbnail generation fails, continue without it
                \Log::warning("Failed to generate thumbnail {$sizeName}: " . $e->getMessage());
            }
        }

        return $urls;
    }

    /**
     * Upload video.
     */
    public function uploadVideo(UploadedFile $file, string $directory = 'videos'): string
    {
        // Validate video
        if (!in_array($file->getMimeType(), ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo'])) {
            throw new \InvalidArgumentException('Invalid video type. Only MP4, MPEG, MOV, and AVI are allowed.');
        }

        // Check file size (max 100MB)
        if ($file->getSize() > 100 * 1024 * 1024) {
            throw new \InvalidArgumentException('Video file size must not exceed 100MB.');
        }

        // Generate unique filename
        $filename = $this->generateFilename($file);
        
        // Store video
        $path = $file->storeAs($directory, $filename, 'public');

        return Storage::url($path);
    }
}
