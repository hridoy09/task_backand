<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as InterventionImage; // Use the Facade
use Exception;
use InvalidArgumentException;

class ImageManager
{
    protected string $disk;
    protected string $defaultDirectory;
    protected int $defaultQuality;

    /**
     * ImageManager constructor.
     *
     * @param string $disk The default storage disk to use.
     * @param string $defaultDirectory The default base directory for uploads.
     * @param int $defaultQuality The default image quality (0-100).
     */
    public function __construct(string $disk = 'public', string $defaultDirectory = 'images', int $defaultQuality = 85)
    {
        $this->disk = $disk;
        $this->defaultDirectory = $defaultDirectory;
        $this->defaultQuality = $defaultQuality;

        // Ensure the default directory uses forward slashes and doesn't end with one
        $this->defaultDirectory = rtrim(str_replace('\\', '/', $this->defaultDirectory), '/');
    }

    /**
     * Set the storage disk.
     *
     * @param string $disk
     * @return $this
     */
    public function disk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * Set the default directory for uploads.
     *
     * @param string $directory
     * @return $this
     */
    public function directory(string $directory): self
    {
        $this->defaultDirectory = rtrim(str_replace('\\', '/', $directory), '/');
        return $this;
    }

    /**
     * Set the default image quality.
     *
     * @param int $quality
     * @return $this
     */
    public function quality(int $quality): self
    {
        if ($quality < 0 || $quality > 100) {
            throw new InvalidArgumentException('Quality must be between 0 and 100.');
        }
        $this->defaultQuality = $quality;
        return $this;
    }

    /**
     * Upload and process an image.
     *
     * @param UploadedFile $file The uploaded file instance.
     * @param string|null $directory Specific directory for this upload (relative to defaultDirectory).
     * @param string|null $filename Desired filename without extension. Generates unique if null.
     * @param array|null $resizeOptions Associative array for resizing: ['width' => W, 'height' => H, 'aspectRatio' => bool, 'upsize' => bool, 'crop' => bool]
     *                                   'crop' uses Intervention's fit() method. If false, uses resize().
     * @param bool $convertToWebP Whether to convert the image to WebP format.
     * @param int|null $quality Image quality for this specific upload.
     * @return string|false The relative path to the saved image or false on failure.
     * @throws Exception
     */
    public function upload(
        UploadedFile $file,
        ?string $directory = null,
        ?string $filename = null,
        ?array $resizeOptions = null,
        bool $convertToWebP = false,
        ?int $quality = null
    ): string|false {
        if (!$file->isValid()) {
            throw new InvalidArgumentException('Invalid file uploaded.');
        }

        $currentQuality = $quality ?? $this->defaultQuality;
        $originalExtension = $file->getClientOriginalExtension();
        $targetExtension = $convertToWebP ? 'webp' : strtolower($originalExtension);

        if (empty($filename)) {
            $filename = Str::random(20) . '_' . time();
        } else {
            $filename = Str::slug($filename); // Sanitize provided filename
        }

        $finalFilename = $filename . '.' . $targetExtension;
        $uploadDirectory = $this->defaultDirectory;
        if ($directory) {
            $uploadDirectory .= '/' . ltrim(str_replace('\\', '/', $directory), '/');
            $uploadDirectory = rtrim($uploadDirectory, '/');
        }

        $relativePath = $uploadDirectory . '/' . $finalFilename;

        try {
            $image = InterventionImage::make($file->getRealPath());

            // Resize logic
            if ($resizeOptions && isset($resizeOptions['width']) && isset($resizeOptions['height'])) {
                $width = (int) $resizeOptions['width'];
                $height = (int) $resizeOptions['height'];
                $aspectRatio = $resizeOptions['aspectRatio'] ?? true; // Keep aspect ratio by default
                $upsize = $resizeOptions['upsize'] ?? false;       // Don't upsize by default
                $crop = $resizeOptions['crop'] ?? false;           // Don't crop by default

                if ($crop) { // Uses fit method to crop to exact dimensions
                    $image->fit($width, $height, function ($constraint) use ($upsize) {
                        if ($upsize) {
                            $constraint->upsize();
                        }
                    });
                } else { // Uses resize method
                    $image->resize($width, $height, function ($constraint) use ($aspectRatio, $upsize) {
                        if ($aspectRatio) {
                            $constraint->aspectRatio();
                        }
                        if ($upsize) {
                            $constraint->upsize();
                        }
                    });
                }
            }

            // Convert to WebP if requested
            if ($convertToWebP) {
                $encodedImage = $image->encode('webp', $currentQuality);
            } else {
                $encodedImage = $image->encode($targetExtension, $currentQuality);
            }

            // Ensure the directory exists
            Storage::disk($this->disk)->makeDirectory($uploadDirectory);

            // Store the image
            $success = Storage::disk($this->disk)->put($relativePath, (string) $encodedImage);

            return $success ? $relativePath : false;

        } catch (Exception $e) {
            // Log the error or handle it as per your application's needs
            // Log::error("Image upload failed: " . $e->getMessage());
            throw $e; // Re-throw for the controller to catch or handle
        }
    }

    /**
     * Delete an image.
     *
     * @param string $relativePath The path relative to the disk's root.
     * @return bool True on success, false on failure.
     */
    public function delete(string $relativePath): bool
    {
        if (empty($relativePath)) {
            return false;
        }

        if (Storage::disk($this->disk)->exists($relativePath)) {
            return Storage::disk($this->disk)->delete($relativePath);
        }
        return false; // File not found
    }

    /**
     * Get the public URL of an image.
     *
     * @param string $relativePath The path relative to the disk's root.
     * @return string|null URL or null if file doesn't exist or disk not public.
     */
    public function url(string $relativePath): ?string
    {
        if (empty($relativePath) || !Storage::disk($this->disk)->exists($relativePath)) {
            return null;
        }
        return Storage::disk($this->disk)->url($relativePath);
    }

    /**
     * Create a thumbnail from an existing image.
     * Saves the thumbnail with a suffix.
     *
     * @param string $originalRelativePath Path of the original image.
     * @param int $width Thumbnail width.
     * @param int $height Thumbnail height.
     * @param string $suffix Suffix for the thumbnail filename (e.g., '_thumb').
     * @param bool $crop Whether to crop or just resize maintaining aspect ratio.
     * @param int|null $quality Thumbnail quality.
     * @return string|false Relative path to the thumbnail or false.
     * @throws Exception
     */
    public function createThumbnail(
        string $originalRelativePath,
        int $width,
        int $height,
        string $suffix = '_thumb',
        bool $crop = true,
        ?int $quality = null
    ): string|false {
        if (!Storage::disk($this->disk)->exists($originalRelativePath)) {
            throw new InvalidArgumentException("Original image not found: {$originalRelativePath}");
        }

        $currentQuality = $quality ?? $this->defaultQuality;
        $pathInfo = pathinfo($originalRelativePath);
        $directory = $pathInfo['dirname'] === '.' ? '' : $pathInfo['dirname']; // Handle root dir
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        $thumbFilename = $filename . $suffix . '.' . $extension;
        $thumbRelativePath = ($directory ? $directory . '/' : '') . $thumbFilename;

        try {
            $imageStream = Storage::disk($this->disk)->get($originalRelativePath);
            $image = InterventionImage::make($imageStream);

            if ($crop) {
                $image->fit($width, $height, function ($constraint) {
                    // $constraint->upsize(); // Optional: allow upsize for fit
                });
            } else {
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    // $constraint->upsize(); // Optional: prevent upsize
                });
            }

            $encodedImage = $image->encode($extension, $currentQuality);
            $success = Storage::disk($this->disk)->put($thumbRelativePath, (string) $encodedImage);

            return $success ? $thumbRelativePath : false;

        } catch (Exception $e) {
            // Log::error("Thumbnail creation failed: " . $e->getMessage());
            throw $e;
        }
    }
}