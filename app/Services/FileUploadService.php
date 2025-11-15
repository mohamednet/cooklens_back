<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
}
