<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

trait HandleImageUploads
{
    public function convertToWebp($path, $disk = 'public')
    {
        if (!$path)
            return null;

        $absolutePath = Storage::disk($disk)->path($path);

        if (!file_exists($absolutePath))
            return $path;

        $filename = pathinfo($path, PATHINFO_FILENAME) . '_' . Str::random(5) . '.webp';
        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $newPath = ($directory === '.' ? '' : $directory . '/') . $filename;
        $newAbsolutePath = Storage::disk($disk)->path($newPath);

        try {
            $image = Image::read($absolutePath);
            $image->toWebp(80)->save($newAbsolutePath);


            Storage::disk($disk)->delete($path);

            return $newPath;
        } catch (\Exception $e) {
            \Log::error('WebP conversion failed: ' . $e->getMessage());
            return $path;
        }
    }
}
