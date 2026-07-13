<?php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadHelper
{
    /**
     * Upload and automatically compress image files to under 1MB.
     * Non-image files are stored as-is.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $folder
     * @param  string  $disk
     * @return string  Saved file path
     */
    public static function uploadAndCompress($file, $folder, $disk = 'public')
    {
        $mime = $file->getMimeType();
        $isImage = str_starts_with($mime, 'image/');

        if ($isImage) {
            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getRealPath());

                // Scale down the image dimensions if it's too large.
                // 1200px max width/height is more than enough for display and keeps quality high while processing faster.
                $image->scaleDown(width: 1200, height: 1200);

                // Force conversion of non-webp formats (png, bmp, etc.) to JPEG.
                // PNG compression in GD is lossless and extremely slow/CPU-heavy.
                // Converting to JPEG 75% is extremely fast and drops file size by 95%.
                $originalExtension = strtolower($file->getClientOriginalExtension());
                $extension = ($originalExtension === 'webp') ? 'webp' : 'jpg';

                if ($extension === 'webp') {
                    $encoded = $image->toWebp(75);
                } else {
                    $encoded = $image->toJpeg(75);
                }

                // If somehow the encoded string is still over 1MB (extremely rare for 1200px at 75 quality),
                // we can recursively scale it down further or lower quality.
                if (strlen($encoded) > 1024 * 1024) {
                    $image->scaleDown(width: 800, height: 800);
                    if ($extension === 'webp') {
                        $encoded = $image->toWebp(60);
                    } else {
                        $encoded = $image->toJpeg(60);
                    }
                }

                // Generate a unique file name
                $filename = Str::uuid() . '.' . $extension;
                $path = rtrim($folder, '/') . '/' . $filename;

                // Save to the specified disk
                Storage::disk($disk)->put($path, (string) $encoded);

                return $path;
            } catch (\Exception $e) {
                // Fallback to default storage if compression fails
                logger()->error('Image compression failed: ' . $e->getMessage());
                return $file->store($folder, $disk);
            }
        }

        // Non-image files (like PDF) are stored as-is
        return $file->store($folder, $disk);
    }
}
