<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

if (! function_exists('uploadImage')) {
    function uploadImage(UploadedFile $image, string $path): string
    {
        $image->store($path);

        return $path . $image->hashName();
    }
}

if (! function_exists('updateImage')) {
    function updateImage(UploadedFile $image, string $oldPath, string $path): string
    {
        deleteImage($oldPath);

        return uploadImage($image, $path);
    }
}

if (! function_exists('deleteImage')) {
    function deleteImage(string $path): bool
    {
        if (! Storage::exists($path)) {
            return false;
        }
        Storage::delete($path);

        return true;
    }
}
