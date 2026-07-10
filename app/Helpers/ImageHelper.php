<?php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageHelper
{
    /**
     * Kompres, resize, dan optimize foto absensi
     * Target: Maksimal 1MB dengan kualitas optimal
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $maxSizeInBytes Default: 1048576 (1MB)
     * @param int $initialQuality Default: 85
     * @return string Path file yang sudah dikompres
     */
    public static function compress($file, $maxSizeInBytes = 1048576, $initialQuality = 85)
    {
        // Jika file sudah <= target, simpan langsung tanpa kompresi
        if ($file->getSize() <= $maxSizeInBytes) {
            return $file->store('absensi', 'public');
        }

        // Inisialisasi Image Manager dengan driver GD
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());

        // Resize otomatis jika terlalu besar (max 1920x1920)
        $image->scale(1920, 1920);

        // Kompresi bertahap dengan quality menurun sampai ukuran target tercapai
        $quality = $initialQuality;
        $encoded = null;

        do {
            $encoded = $image->encode('jpg', $quality);
            $quality -= 5; // Turunkan quality 5% setiap iterasi
        } while ($encoded->length() > $maxSizeInBytes && $quality >= 20);

        // Generate nama file unik
        $filename = time() . '_' . uniqid() . '.jpg';
        $path = 'absensi/' . $filename;
        $fullPath = storage_path('app/public/' . $path);

        // Pastikan folder destination ada
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        // Simpan hasil kompresi
        $encoded->toFile($fullPath);

        return $path;
    }
}
