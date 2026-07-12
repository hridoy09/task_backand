<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;



class FileManager
{
    /**
     * Upload a file to the public disk (accessible via URL).
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $oldFile
     * @param array|null $resize ['width' => int, 'height' => int]
     * @return string
     */
    public static function uploadPublic(UploadedFile $file, string $directory, ?string $oldFile = null, ?array $resize = null): string
    {
        // Remove old file if it exists
        if ($oldFile && Storage::disk('assets')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }

        // Generate a unique filename
        $filename = uniqid('', true) . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;

        // Handle image upload with optional resize
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());

            if ($resize && isset($resize['width'], $resize['height'])) {
                $image->resize($resize['width'], $resize['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            Storage::disk('public')->put($path, (string) $image->encode());
        } else {
            // Normal file upload
            Storage::disk('public')->put($path, file_get_contents($file));
        }

        return $path;
    }


public static function uploadToAssets(UploadedFile $file, string $directory, ?string $oldFile = null, ?array $resize = null): string
{
    $assetsPath = trim($directory, '/');

    if (!file_exists($assetsPath)) {
        mkdir($assetsPath, 0755, true);
    }

    if ($oldFile && file_exists($oldFile)) {
        unlink($oldFile);
    }

    $filename = uniqid('', true) . '.' . $file->getClientOriginalExtension();
    $fullPath = $assetsPath . '/' . $filename;

    // Safely detect MIME type
    $mimeType = $file->getMimeType();

    if ($mimeType && str_starts_with($mimeType, 'image/')) {
        // Handle image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());

        if ($resize && isset($resize['width'], $resize['height'])) {
            $image->resize($resize['width'], $resize['height'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $image->save($fullPath);
    } else {
        // Fallback normal file upload
        $file->move($assetsPath, $filename);
    }

    return $directory . '/' . $filename;
}



    /**
     * Upload a file to the private (local) disk (not publicly accessible).
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $oldFile
     * @return string
     */
    public static function uploadPrivate(UploadedFile $file, string $directory, ?string $oldFile = null): string
    {
        // Remove old file if it exists
        if ($oldFile && Storage::disk('local')->exists($oldFile)) {
            Storage::disk('local')->delete($oldFile);
        }

        return Storage::disk('local')->put($directory, $file);
    }

    /**
     * Get the full URL to a publicly stored file.
     *
     * @param string|null $path
     * @return string|null
     */
    public static function getPublicUrl(?string $path): ?string
    {
        return asset($path);
    }
}
