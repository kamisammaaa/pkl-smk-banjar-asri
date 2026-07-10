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
                // 1600px max width/height is more than enough for display and keeps quality high.
                $image->scaleDown(width: 1600, height: 1600);

                // Determine file extension
                $extension = strtolower($file->getClientOriginalExtension());
                if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $extension = 'jpg';
                }

                // Compress image to ensure it is under 1MB.
                // Normally scaleDown to 1600 + quality 75/80 results in 100KB-400KB.
                // We'll start with 75% quality.
                if ($extension === 'png') {
                    // For PNG, we can use toPng()
                    $encoded = $image->toPng();
                } elseif ($extension === 'webp') {
                    $encoded = $image->toWebp(75);
                } else {
                    $encoded = $image->toJpeg(75);
                }

                // If somehow the encoded string is still over 1MB (extremely rare for 1600px at 75 quality),
                // we can recursively scale it down further or lower quality.
                if (strlen($encoded) > 1024 * 1024) {
                    $image->scaleDown(width: 1000, height: 1000);
                    if ($extension === 'png') {
                        $encoded = $image->toPng();
                    } elseif ($extension === 'webp') {
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
